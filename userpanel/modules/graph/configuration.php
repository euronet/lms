<?php

/*
 *  $Id: configuration.php,v 1.0 2010/03/23 21:00:00 emers Exp $
 */

$USERPANEL->AddModule(trans('Wykresy'),	// Display name
		    'graph', 		// Module name - must be the same as directory name
		    trans('Wykresy wykorzystania internetu'), // Tip 
		    15,			// Priority
		    trans('Ten moduł pokazuje wykres obciążenia portu klienta')	// Description
		    );

?>