<?php

function toSafeValue(string $string) : string {
	$string = str_replace(" ", "&nbsp;", $string);
	$string = str_replace("\"", "&quot;", $string);
	$string = str_replace("<", "&lt;", $string);
	$string = str_replace(">", "&gt;", $string);
	return $string;
}