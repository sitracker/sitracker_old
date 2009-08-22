<?php
// notice.class.php - The representation of a group within sit
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:  Paul Heaney <paul[at]sitracker.org>


class Notice extends SitEntity {

    function Notice($id=0)
    {
        if ($id > 0)
        {
            $this->id = $id;
            $this->retrieveDetails();
        }
    }


    function retrieveDetails()
    {
        trigger_error("Notice.retrieveDetails() not yet implemented");
    }


    function add()
    {
        trigger_error("Notice.add() not yet implemented");
    }


    function edit()
    {
        trigger_error("Notice.edit() not yet implemented");
    }

    function getSOAPArray()
    {
        trigger_error("Notice.getSOAPArray() not yet implemented");
    }
}
?>
