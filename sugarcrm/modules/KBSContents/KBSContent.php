<?php
/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2014 SugarCRM Inc.  All rights reserved.
 */
class KBSContent extends SugarBean {

    public $table_name = "kbscontents";
    public $object_name = "KBSContent";
    public $new_schema = true;
    public $module_dir = 'KBSContents';

    /**
     * Return primary language for KB.
     * @return array Key and label for primary language.
     */
    public function getPrimaryLanguage()
    {
        $admin = BeanFactory::getBean('Administration');
        $config = $admin->getConfigForModule('KBSDocuments');
        $langs = $config['languages'];
        $default = null;
        foreach ($langs as $lang) {
            if ($lang['primary'] === true) {
                $default = $lang;
                unset($default['primary']);
                $default = array(
                    'label' => reset($default),
                    'key' => key($default)
                );
                break;
            }
        }
        return $default;
    }

    /**
     * {@inheritDoc}
     */
    public function save($check_notify = false)
    {
        if (!SugarBean::inOperation('saving_related')) {
            if (!$this->id || $this->new_with_id) {
                if (!$this->id) {
                    $this->id = create_guid();
                    $this->new_with_id = true;
                }

                $doc = $article = null;

                if (empty($this->kbsdocument_id)) {
                    $doc = BeanFactory::getBean('KBSDocuments');
                    $doc->new_with_id = true;
                    $doc->id = create_guid();
                    $doc->name = $this->name;
                    $doc->save();
                    $this->load_relationship('kbsdocuments_kbscontents');
                    $this->kbsdocuments_kbscontents->add($doc);
                }

                if (empty($this->kbsarticle_id)) {
                    $article = BeanFactory::getBean('KBSArticles');
                    $article->new_with_id = true;
                    $article->id = create_guid();
                    $article->name = $this->name;
                    $article->save();
                    $this->load_relationship('kbsarticles_kbscontents');
                    $this->kbsarticles_kbscontents->add($article);
                }

                if (!empty($article) && !empty($doc)) {
                    $article->load_relationship('kbsdocuments_kbsarticles');
                    $article->kbsdocuments_kbsarticles->add($doc);
                }

                if (empty($this->language)) {
                    $lang = $this->getPrimaryLanguage();
                    $this->language = $lang['key'];
                }

                $this->active_rev = (int) !empty($article);

                if (empty($this->revision)) {
                    $query = new SugarQuery();
                    $query->from(BeanFactory::getBean('KBSContents'));
                    $query->select(array('id'))->fieldRaw('MAX(revision)', 'max_revision');
                    $query->where()
                        ->equals('kbsdocument_id', $this->kbsdocument_id)
                        ->equals('kbsarticle_id', $this->kbsarticle_id);

                    $result = $query->execute();
                    $this->revision = !empty($result[0]['max_revision']) ? $result[0]['max_revision'] + 1 : 1;
                }
            }
        }
        return parent::save($check_notify);
    }

    /**
     * {@inheritDoc}
     */
    public function mark_deleted($id)
    {
        if ($this->active_rev == 1) {
            $query = new SugarQuery();
            $query->from(BeanFactory::getBean('KBSContents'));
            $query->select(array('id'));
            $query->where()
                ->notEquals('id', $this->id)
                ->equals('kbsdocument_id', $this->kbsdocument_id)
                ->equals('kbsarticle_id', $this->kbsarticle_id);
            $query->orderBy('date_entered', 'DESC');
            $query->limit(1);

            $result = $query->execute();

            if ($result) {
                $bean = BeanFactory::getBean('KBSContents', $result[0]['id']);
                if ($bean->id) {
                    $this->resetActivRev();

                    $bean->active_rev = 1;
                    $bean->save();
                }
            }
        }
        parent::mark_deleted($id);
    }

    /**
     * Reset active revision status for all revisions in article.
     * @param SugarBean $bean
     */
    protected function resetActivRev($bean = null)
    {
        $bean = ($bean === null) ? $this : $bean;
        $query = "UPDATE {$bean->table_name}
                    SET active_rev = 0
                    WHERE
                      kbsdocument_id = {$bean->db->quoted($bean->kbsdocument_id)} AND
                      kbsarticle_id = {$bean->db->quoted($bean->kbsarticle_id)}
                ";
        $bean->db->query($query);
    }

    /**
     * {@inheritdoc}
     **/
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function get_summary_text()
    {
        return $this->name;
    }
}
