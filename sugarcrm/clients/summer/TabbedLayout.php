<?php
require_once('clients/summer/SideBarLayout.php');

class TabbedLayout extends SideBarLayout {
    protected $containers = array('main' => array());
    protected $layout = array(
        'type' => 'record-bottom',
        'components' =>
        array(),
    );

    protected function getMainLayout() {
        return $this->containers['main'];

    }

    public function getLayout() {
        if (empty($this->containers['side'])) {
            $this->spans['main'] = 12;
        }

        $this->layout['components'] = $this->getMainLayout();
        return $this->layout;
    }


}
