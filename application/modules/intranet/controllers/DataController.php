<?php

class Intranet_DataController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function eventsAction()
    {
        $this->_helper->layout->setLayout('datalayout');

        $events = new Application_Model_DbIcsData_Events();
        $select = $events->select();
        $select->setIntegrityCheck(false);

        $select->from(array('t1' => 'jos_eb_events'), 
                      array(
                          't1.id', 
                          't1.category_id', 
                          't1.location_id', 
                          't1.title', 
                          't1.event_date', 
                          't1.short_description',
                          'count(a.id) as total',
                          't1.event_capacity',
                          'IF (discount_type=1, concat(round(discount),"%"), concat(Round(discount),"%")) as disc',
                          '( select group_concat(distinct(category_id)) from jos_eb_event_categories where  t1.id = event_id) as ID',
                          'sum(a.amount) as Income', 
                          'c.points'
                          ))
                ->joinLeft(array('a'=>'jos_eb_registrants'), 'a.event_id=t1.id')
                ->joinLeft(array('c'=>'cpd_events'), 'c.event_id=t1.id')
                ->where('a.published=1 OR a.payment_method = "os_offline" or a.id is null or a.payment_method = "ps_oneclick" or a.payment_method = "os_member"',
                        'and event_date >= NOW() and t1.published=1')
                ->group(array(
                        't1.id',
                        'title',
                        'event_date',
                        'short_description'
                        ))
                ->order("event_date asc")
                ->limit(80);
                


        $this->view->rows = $events->fetchAll($select);
    }

    public function pasteventsAction()
    {
        $this->_helper->layout->setLayout('datalayout');

        $events = new Application_Model_DbIcsData_Events();
        $select = $events->select();
        $select->setIntegrityCheck(false);

        $select->from(array('t1' => 'jos_eb_events'), 
                      array(
                          't1.id', 
                          't1.title', 
                          't1.event_date', 
                          't1.short_description',
                          'count(r.id) as total',
                          'sum(r.attended) as att',
                          'sum(r.amount) as Income'
                         ))
                ->joinLeft(array('r'=>'jos_eb_registrants'), 'r.event_id=t1.id')
                ->where('(r.published=1 OR r.payment_method in ("os_offline", "ps_oneclick") OR r.id is null) and event_date <= NOW()')
                ->group(array(
                        't1.id',
                        'title',
                        'event_date',
                        'short_description'
                        ))
                ->having('count(r.id) >0')
                ->order("event_date desc")
                ->limit(80);
                


        $this->view->rows = $events->fetchAll($select);
    }

    public function noinvoicesAction()
    {
        $this->_helper->layout->setLayout('datalayout');

        $events = new Application_Model_DbIcsData_Events();
        $select = $events->select();
        $select->setIntegrityCheck(false);

        $select->from(array('t1' => 'jos_eb_registrants'), 
                      array(
                          't1.id', 
                          't1.first_name', 
                          't1.last_name',
                          't2.title',
                          't1.sin'
                         ))
                ->joinInner(array('t2'=>'jos_eb_events'), 't2.id=t1.event_id')
                ->where('t1.sin = "SIN000000"')
                ->group(array(
                    't2.id'
                ));
                // ->limit(100);

                echo $select;
                
                


        $this->view->rows = $events->fetchAll($select);
    }
}

?>