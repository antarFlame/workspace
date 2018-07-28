<?php

class modulelements extends def_module {
    public $per_page;

    public function __construct() {
        parent::__construct();

        if(cmsController::getInstance()->getCurrentMode() == "admin") {

            $configTabs = $this->getConfigTabs();
            if ($configTabs) {
                $configTabs->add("config");
            }

            $this->__loadLib("__admin.php");
            $this->__implement("__modulelements");

        } else {

            $this->per_page = regedit::getInstance()->getVal("//modules/modulelements/per_page");
        }

    }

    public function groupelements($path = "", $template = "default") {
        if($this->breakMe()) return;
        $element_id = cmsController::getInstance()->getCurrentElementId();

        templater::pushEditable("modulelements", "groupelements", $element_id);
        return $this->group($element_id, $template) . $this->listElements($element_id, $template);
    }

    public function item_element() {
        if($this->breakMe()) return;
        $element_id = (int) cmsController::getInstance()->getCurrentElementId();
        return $this->view($element_id);
    }

    public function group($elementPath = "", $template = "default", $per_page = false) {
        if($this->breakMe()) return;
        $hierarchy = umiHierarchy::getInstance();
        list($template_block) = def_module::loadTemplates("tpls/modulelements/{$template}.tpl", "group");

        $elementId = $this->analyzeRequiredPath($elementPath);

        $element = $hierarchy->getElement($elementId);

        templater::pushEditable("modulelements", "groupelements", $element->id);
        return self::parseTemplate($template_block, array('id' => $element->id), $element->id);

    }

    public function view($elementPath = "", $template = "default") {
        if($this->breakMe()) return;
        $hierarchy = umiHierarchy::getInstance();
        list($template_block) = def_module::loadTemplates("tpls/modulelements/{$template}.tpl", "view");

        $elementId = $this->analyzeRequiredPath($elementPath);
        $element = $hierarchy->getElement($elementId);

        templater::pushEditable("modulelements", "item_element", $element->id);
        return self::parseTemplate($template_block, array('id' => $element->id), $element->id);
    }

    public function listGroup($elementPath, $template = "default", $per_page = false, $ignore_paging = false) {
// Код метода
    }


    public function listElements($elementPath, $template = "default", $per_page = false, $ignore_paging = false) {
// Код метода
    }

    public function config() {
        return __modulelements::config();
    }

    public function getEditLink($element_id, $element_type) {
        $element = umiHierarchy::getInstance()->getElement($element_id);
        $parent_id = $element->getParentId();

        switch($element_type) {
            case "groupelements": {
                $link_add = $this->pre_lang . "/admin/modulelements/add/{$element_id}/item_element/";
                $link_edit = $this->pre_lang . "/admin/modulelements/edit/{$element_id}/";

                return Array($link_add, $link_edit);
                break;
            }

            case "item_element": {
                $link_edit = $this->pre_lang . "/admin/modulelements/edit/{$element_id}/";

                return Array(false, $link_edit);
                break;
            }

            default: {
                return false;
            }
        }
    }

};
?>