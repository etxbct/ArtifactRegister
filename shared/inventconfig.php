<?php

define ('DEBUG',       False);

// Symbols
define ('DOWNARROW',        '&#11167;');
define ('UPARROW',          '&#11165;');
// Lables
define ('LISTNAME',         (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? '&Ouml;sterl&ouml;vsta Hembygdsf&ouml;renings museef&ouml;rem&aring;l' : 'Ouml;sterlouml;fsta Hembygdsf&ouml;renings Inventory list of museum objects')));
define ('LINVENTORYLIST',   (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Inventarielista museef&ouml;rem&aring;l' : 'Inventory list of museum objects')));
define ('LSTORAGELIST',     (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Byggnader' : 'Buildings')));
define ('LINVENTORY',       (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Museef&ouml;rem&aring;l' : 'Museum items')));
define ('LNAME',            (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Namn och anv&auml;ndning:' : 'Name:')));
define ('LINVENTORYNR',     (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Inventarie Nr:' : 'Inventory Nr:')));
define ('LARRIVALDATE',     (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Ankomstdatum:' : 'Arrival date:')));
define ('LORIGIN',          (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'H&auml;rkomst:' : 'Origin:')));
define ('LSUBMITTEDBY',     (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? '&Ouml;verl&auml;mnat av:' : 'Submitted by:')));
define ('LMAKER',           (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Tillverkare:' : 'Maker:')));
define ('LMANUFACTUREYEAR', (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Tillverknings&aring;r:' : 'Manufacture year:')));
define ('LDESCRIPTION',     (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Beskrivning:' : 'Description:')));
define ('LREMARK',          (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Anm&auml;rkning:' : 'Remark:')));
define ('LBUILDING',        (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Byggnad:' : 'Building:')));
define ('LSTORAGEPLACE',    (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'F&ouml;rvaringsplats:' : 'Storage place:')));
define ('LFOTONR',          (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Foto Nr:' : 'Photo nr:')));
define ('LFOTO',            (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Foto:' : 'Photo:')));
define ('LDIVNOTE',         (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Diverse anteckningar:' : 'Miscellaneous notes:')));
define ('LINVENTORIEDYEAR', (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Inventerat &aring;r:' : 'Inventoried year:')));
define ('LFILTERSTORAGE',   (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Filtrera byggnad:' : 'Filter building:')));
define ('LTOTAL',           (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Totalt' : 'Total')));
define ('LPCS',             (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'st' : 'pcs')));
define ('LITEMS',           (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'listade f&ouml;rem&aring;l' : 'listed items')));
define ('LFIND',            (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Fris&ouml;kning:' : 'Free search')));
// Button lables
define ('BLVIEW',           (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Visa' : 'View')));
define ('BLBACK',           (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Tillbaka' : 'Back')));
define ('BLFILTER',         (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'Filtrera' : 'Filter')));
define ('BLFIND',           (utf8_encode((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'sv') ? 'S&ouml;k' : 'Search')));
// Do
define ('DOVIEW',           'view');
define ('DOBACK',           'back');
define ('DOFIND',           'find');
define ('DOFILTER',         'filter');
