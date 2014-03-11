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
class SugarUpgradeRepairUsDollarFields extends UpgradeScript
{
    public $order = 2050;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        // only affects upgrades from Sugar 7.x
        if (version_compare($this->from_version, '7.0', '<')) {
            return;
        }

        // Fix ProductTemplates
        $fields = array(
            'list_price' => 'list_usdollar',
            'cost_price' => 'cost_usdollar',
            'discount_price' => 'discount_usdollar',
        );
        foreach ($fields as $field => $fieldUSDollar) {
            $this->db->query(
                "
            UPDATE product_templates
            SET {$fieldUSDollar} = {$field} / base_rate
            WHERE base_rate > 0
            AND format({$field} / base_rate, 6) <> format({$fieldUSDollar}, 6)
            AND deleted = 0
            "
            );
        }

        // Fix ProductBundles
        $fields = array(
            'total' => 'total_usdollar',
            'subtotal' => 'subtotal_usdollar',
            'shipping' => 'shipping_usdollar',
            'deal_tot' => 'deal_tot_usdollar',
            'new_sub' => 'new_sub_usdollar',
            'tax' => 'tax_usdollar',
        );
        foreach ($fields as $field => $fieldUSDollar) {
            $this->db->query(
                "
            UPDATE product_bundles
            SET {$fieldUSDollar} = {$field} / base_rate
            WHERE base_rate > 0
            AND format({$field} / base_rate, 6) <> format({$fieldUSDollar}, 6)
            AND deleted = 0
            "
            );
        }

    }
}
