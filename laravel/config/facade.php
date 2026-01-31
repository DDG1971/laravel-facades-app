<?php
return [
// фасады, которые вообще не включать в "на пилу"
'exclude_from_saw' => [
'Balustrade',
'BaguetteTall',
'BaguetteTall"Ш"',
'Bent',
'DSP',
'PlugPVC',
'Grid',
'CornerPVC_outer',
'CornerPVC_inside',
'PlinthPVC'
],
// фасады, которые включать, но без +4 мм
'no_addition' => [
'Lhandle',
'uhandle',
'Sparta',
'Bravo',
'Roof',
'Line',
'Pilaster',
'Corner',
],

];
