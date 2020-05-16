<?php

exec('top -b -n 1 -d 3', $out);
$Cpu = explode('  ', $out[2]);
$Mem = explode('  ', $out[3]);
$Swap = explode('  ', $out[4]);
var_dump($Cpu, $Mem, $Swap);

$cpu = str_replace(array('%us,', ' '), '', $Cpu[1]);
$mem = str_replace(array('k used,', ' '), '', $Mem[2]);
$swap = str_replace(array('k cached', ' '), '', $Swap[5]);