<?php
/*
 * Copyright (c) 2009 - 2010, Frosted Design
 * All rights reserved.
 */

namespace hydrogen\view;

use hydrogen\view\Token;
use hydrogen\view\tokens\BlockToken;
use hydrogen\view\tokens\CommentToken;
use hydrogen\view\tokens\FilterToken;
use hydrogen\view\tokens\TextToken;
use hydrogen\view\tokens\VariableToken;

class Lexer {
	const TOKEN_BLOCK = 1;
	const TOKEN_COMMENT = 2;
	const TOKEN_FILTER = 3;
	const TOKEN_TEXT = 4;
	const TOKEN_VARIABLE = 5;
	
	const BLOCK_OPENTAG = "{%";
	const BLOCK_CLOSETAG = "%}";
	const BLOCK_COMMAND_ARG_SEPARATOR = " ";
	const COMMENT_OPENTAG = "{#";
	const COMMENT_CLOSETAG = "#}";
	const VARIABLE_OPENTAG = "{{";
	const VARIABLE_CLOSETAG = "}}";
	const VARIABLE_LEVEL_SEPARATOR = ".";
	const VARIABLE_FILTER_SEPARATOR = "|";
	const VARIABLE_FILTER_ARGUMENT_SEPARATOR = ":";
	
	public static function tokenize($origin, $data) {
		$splitRegex = '/(' .
			self::VARIABLE_OPENTAG . '.*' . self::VARIABLE_CLOSETAG . '|' .
			self::BLOCK_OPENTAG . '.*' . self::BLOCK_CLOSETAG . '|' .
			self::COMMENT_OPENTAG . '.*' . self::COMMENT_CLOSETAG .
			')/U';
		$lines = preg_split($splitRegex, $data, null,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
		$tokens = array();
		foreach ($lines as $line) {
			// Check for variable tag
			if (static::surroundedBy($line, self::VARIABLE_OPENTAG,
				self::VARIABLE_CLOSETAG)) {
				$tokens[] = static::getVariableToken($origin,
					trim(substr($line, strlen(self::VARIABLE_OPENTAG),
					strlen($line) - strlen(self::VARIABLE_OPENTAG) -
					strlen(self::VARIABLE_CLOSETAG))));
			}
			// Check for block tag
			else if (static::surroundedBy($line, self::BLOCK_OPENTAG,
				self::BLOCK_CLOSETAG)) {
				$tokens[] = static::getBlockToken($origin,
					trim(substr($line, strlen(self::BLOCK_OPENTAG),
					strlen($line) - strlen(self::BLOCK_OPENTAG) -
					strlen(self::BLOCK_CLOSETAG))));
			}
			// Check for comment tag
			else if (static::surroundedBy($line, self::COMMENT_OPENTAG,
				self::COMMENT_CLOSETAG)) {
				$tokens[] = new CommentToken($origin,
					trim(substr($line, strlen(self::COMMENT_OPENTAG),
					strlen($line) - strlen(self::COMMENT_OPENTAG) -
					strlen(self::COMMENT_CLOSETAG))));
			}
			// It must be text!  But skip it if it's empty.
			else if (($text = trim($line)) !== '')
				$tokens[] = new TextToken($origin, $text);
		}
		return $tokens;
	}
	
	protected static function getBlockToken($origin, $data) {
		
	}
	
	protected static function getVariableToken($origin, $data) {
		
	}
	
	protected static function surroundedBy($haystack, $startsWith, $endsWith) {
		$sLen = strlen($startsWith);
		$eLen = strlen($endsWith);
		if (strlen($haystack) >= $sLen + $eLen) {
			return substr_compare($haystack, $startsWith, 0, $sLen) === 0 &&
				substr_compare($haystack, $endsWith, -$eLen, $eLen) === 0;
		}
		return false;
	}

	protected function __construct() {}
}

?>