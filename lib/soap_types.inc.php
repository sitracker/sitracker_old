<?php
// soap_types.inc.php - The types used by SIT! soap implementation
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Paul Heaney <paul[at]sitracker.org>

require (APPLICATION_LIBPATH . 'soap_error_definitions.inc.php');

$server->wsdl->addComplexType('status_value',
                                        'complexType',
                                        'struct',
                                        'all',
                                        '',
                                        array ('value' => array ('name' => 'value', 'type' => 'xsd:int'),
                                                'name' => array ('name' => 'name', 'type' => 'xsd:string'),
                                                'description' => array ('name' => 'description', 'type' => 'xsd:string'))
                                        );

$server->wsdl->addComplexType('login_response',
                                        'complexType',
                                        'struct',
                                        'all',
                                        '',
                                        array('sessionid' => array('name' => 'sessionid', 'type' => 'xsd:string'),
                                                'status' => array('name' => 'status', 'type' => 'tns:status_value'))
                                    );

$server->wsdl->addComplexType('logout_response',
                                        'complexType',
                                        'struct',
                                        'all',
                                        '',
                                        array('status' => array('name' => 'status', 'type' => 'tns:status_value'))
                                    );

$server->wsdl->addComplexType('incident',
                                        'complexType',
                                        'struct',
                                        'all',
                                        '',
                                        array('incidentid' => array ('name' => 'incidentid', 'type' => 'xsd:int'),
                                                'title' => array ('name' => 'title', 'type' => 'xsd:string'),
                                                'ownerid' => array('name' => 'ownerid', 'type' => 'xsd:int'),
                                                'townerid' => array('name' => 'townerid', 'type' => 'xsd:int'),
                                                'owner' => array('name' => 'owner', 'type' => 'xsd:string'),
                                                'towner' => array('name' => 'towner', 'type' => 'xsd:string'),
                                                'skillid' => array('name' => 'skillid', 'type' => 'xsd:int'),
                                                'skill' => array('name' => 'skill', 'type' => 'xsd:string'),
                                                'maintenanceid' => array('name' => 'maintenanceid', 'type' => 'xsd:int'),
                                                'maintenance' => array('name' => 'maintenance', 'type' => 'xsd:string'),
                                                'priorityid' => array('name' => 'priorityid', 'type' => 'xsd:int'),
                                                'priority' => array('name' => 'priority', 'type' => 'xsd:string'),
                                                'currentstatusid' => array('name' => 'currentstatusid', 'type' => 'xsd:int'),
                                                'currentstatusinternal' => array('name' => 'currentstatusinternal', 'type' => 'xsd:string'),
                                                'currentstatusexternal' => array('name' => 'currentstatusexternal', 'type' => 'xsd:string'),
                                                'servicelevel' => array('name' => 'servicelevel', 'type' => 'xsd:string')
                                            )
                                    );

$server->wsdl->addComplexType('incident_list',
                                            'complexType',
                                            'array',
                                            '',
                                            'SOAP-ENC:Array',
                                            array(),
                                            array( array ('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:incident[]')),
                                            'tns:incident'
                                        );

$server->wsdl->addComplexType('incident_list_response',
                                            'complexType',
                                            'struct',
                                            'all',
                                            '',
                                            array ('incidents' => array('name' => 'incident', 'type' => 'tns:incident_list'),
                                                    'status' => array('name' => 'status', 'type' => 'tns:status_value')
                                            )
                                        );


/**
 * The class which represents the status which SiT! always returns when using the SOAP API
 * @author Paul Heaney
 */
class SoapStatus
{
    var $value;
    var $name;
    var $description;

    /**
     * Creates a new SoapStatus object.
     * @author Paul Heaney
     */
    function __construct()
    {
        $this->set_error('no_error');
    }

    /**
     * Sets the error code for this object
     * @param string $name. Name of the error as defined in soap_error_definitions
     * @author Paul Heaney
     */
    function set_error($name)
    {
        global $soap_errors;
        if (isset($soap_errors[$name]))
        {
            $this->value = $soap_errors[$name]['value'];
            $this->name = $soap_errors[$name]['name'];
            $this->description = $soap_errors[$name]['description'];
        }
        else
        {
            $this->value = -1;
            $this->name = "Undefined error {$name} occured";
            $this->description = "Undefined error {$name} occured";
        }
    }

    /**
     * Generate the array to be returned by nusoap
     * @return array. Status array
     * @author Paul Heaney
     */
    function get_array()
    {
        return array('value' => $this->value, 'name' => $this->name, 'description' => $this->description);
    }
}

/**
 * Incident class for SiT, represents a single incident within SiT
 * @author Paul Heaney
 * @todo FIXME move out of this file into incidents in SiT3.60, extend SitEntity and make more useful
 */
class Incident
{
    var $incidentid = -1;
    var $title = "no title";
    var $ownerid = -1;
    var $townerid = -1;
    var $owner = "no owner";
    var $towner = "no temp owner";
    var $skillid = -1;
    var $skill = "no skill";
    var $maintenanceid = -1;
    var $maintenance = "no maintenance";
    var $priorityid = -1;
    var $priority = "no priority";
    var $currentstatusid = -1;
    var $currentstatusinternal = "no status";
    var $currentstatusexternal = "no status";
    var $servicelevel = "no service level";


    /**
     * Returns the array of the incident required by nusoap
     * @return array. Array for NUSOAP
     * @author Paul Heaney 
     */
    function get_array()
    {
        debug_log("get_array ".$this->incidentid );
        return array('incidentid' => $this->incidentid,
                            'title' => $this->title,
                            'ownerid' => $this>ownerid,
                            'townerid' => $this->townerid,
                            'owner' => $this->owner,
                            'towner' => $this->towner,
                            'skillid' => $this->skillid,
                            'skill' => $this->skill,
                            'maintenanceid' => $this->maintenanceid,
                            'maintenance' => $this->maintenance,
                            'priorityid' => $this->priorityid,
                            'priority' => $this->priority,
                            'currentstatusid' => $this->currentstatusid,
                            'currentstatusinternal' => $this->currentstatusinternal,
                            'currentstatusexternal' => $this->currentstatusexternal,
                            'servicelevel' => $this->servicelevel
                        );
    }

}

?>
