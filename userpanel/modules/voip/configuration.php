<?php

/*
 *  $Id: configuration.php,v 1.0 2010/03/23 21:00:00 emers Exp $
 */

$USERPANEL->AddModule(trans('Telefon'),	// Display name
		    'voip', 		// Module name - must be the same as directory name
		    trans('Karta telefoniczna'), // Tip 
		    20,			// Priority
		    trans('Ten modu³ daje wgl¹d w biling telefoczniczny')	// Description
		    );

?>