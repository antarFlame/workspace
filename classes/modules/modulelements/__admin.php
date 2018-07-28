<?php
abstract class __modulelements extends baseModuleAdmin {

    public function config() {
        $regedit = regedit::getInstance();
        $params = array('config' => array('int:per_page' => NULL));
        $mode = getRequest("param0");

        if($mode == "do") {
            $params = $this->expectParams($params);
            $regedit->setVar("//modules/modulelements/per_page", $params['config']['int:per_page']);
            $this->chooseRedirect();
        }

        $params['config']['int:per_page'] = (int) $regedit->getVal("//modules/modulelements/per_page");

        $this->setDataType("settings");
        $this->setActionType("modify");

        $data = $this->prepareData($params, "settings");

        $this->setData($data);
        return $this->doData();
    }


    public function lists() {
        $this->setDataType("list");
        $this->setActionType("view");

        if($this->ifNotXmlMode()) return $this->doData();

        $limit = 20;
        $curr_page = getRequest('p');
        $offset = $curr_page * $limit;

        $sel = new selector('pages');
        $sel->types('hierarchy-type')->name('modulelements', 'groupelements');
        $sel->types('hierarchy-type')->name('modulelements', 'item_element');
        $sel->limit($offset, $limit);

        selectorHelper::detectFilters($sel);

        $data = $this->prepareData($sel->result, "pages");

        $this->setData($data, $sel->length);
        $this->setDataRangeByPerPage($limit, $curr_page);
        return $this->doData();
    }

    public function add() {
        $parent = $this->expectElement("param0");
        $type = (string) getRequest("param1");
        $mode = (string) getRequest("param2");

        $this->setHeaderLabel("header-modulelements-add-" . $type);

        $inputData = Array(	"type" => $type,
            "parent" => $parent,
            'type-id' => getRequest('type-id'),
            "allowed-element-types" => Array('groupelements', 'item_element'));

        if($mode == "do") {
            $element_id = $this->saveAddedElementData($inputData);
            if($type == "item") {
                umiHierarchy::getInstance()->moveFirst($element_id, ($parent instanceof umiHierarchyElement)?$parent->getId():0);
            }
            $this->chooseRedirect();
        }

        $this->setDataType("form");
        $this->setActionType("create");

        $data = $this->prepareData($inputData, "page");

        $this->setData($data);
        return $this->doData();
    }


    public function edit() {
        $element = $this->expectElement('param0', true);
        $mode = (string) getRequest('param1');

        $this->setHeaderLabel("header-modulelements-edit-" . $this->getObjectTypeMethod($element->getObject()));

        $inputData = array(
            'element'				=> $element,
            'allowed-element-types'	=> array('groupelements', 'item_element')
        );

        if($mode == "do") {
            $this->saveEditedElementData($inputData);
            $this->chooseRedirect();
        }

        $this->setDataType("form");
        $this->setActionType("modify");

        $data = $this->prepareData($inputData, "page");

        $this->setData($data);
        return $this->doData();
    }


    public function del() {
        $elements = getRequest('element');
        if(!is_array($elements)) {
            $elements = array($elements);
        }

        foreach($elements as $elementId) {
            $element = $this->expectElement($elementId, false, true);

            $params = array(
                "element" => $element,
                "allowed-element-types" => Array('groupelements', 'item_element')
            );
            $this->deleteElement($params);
        }

        $this->setDataType("list");
        $this->setActionType("view");
        $data = $this->prepareData($elements, "pages");
        $this->setData($data);

        return $this->doData();
    }


    public function activity() {
        $elements = getRequest('element');
        if(!is_array($elements)) {
            $elements = array($elements);
        }
        $is_active = getRequest('active');

        foreach($elements as $elementId) {
            $element = $this->expectElement($elementId, false, true);

            $params = array(
                "element" => $element,
                "allowed-element-types" => Array('groupelements', 'item_element'),
                "activity" => $is_active
            );
            $this->switchActivity($params);
            $element->commit();
        }

        $this->setDataType("list");
        $this->setActionType("view");
        $data = $this->prepareData($elements, "pages");
        $this->setData($data);

        return $this->doData();
    }


    public function getDatasetConfiguration($param = '') {
        return array(
            'methods' => array(
                array('title'=>getLabel('smc-load'), 'forload'=>true, 'module'=>'modulelements', '#__name'=>'lists'),
                array('title'=>getLabel('smc-delete'),'module'=>'modulelements', '#__name'=>'del', 'aliases' => 'tree_delete_element,delete,del'),
                array('title'=>getLabel('smc-activity'),'module'=>'modulelements', '#__name'=>'activity', 'aliases' => 'tree_set_activity,activity'),
                array('title'=>getLabel('smc-copy'), 'module'=>'content', '#__name'=>'tree_copy_element'),
                array('title'=>getLabel('smc-move'),'module'=>'content', '#__name'=>'tree_move_element'),
                array('title'=>getLabel('smc-change-template'), 'module'=>'content', '#__name'=>'change_template'),
                array('title'=>getLabel('smc-change-lang'), 'module'=>'content', '#__name'=>'move_to_lang')),
            'types' => array(
                array('common' => 'true', 'id' => 'item_element')
            ),
            'stoplist' => array(),
            'default' => 'h1[140px]'
        );
    }
};
?>