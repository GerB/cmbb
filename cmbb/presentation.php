<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\cmbb;

class presentation
{

	/**
	 * Strip empty tags from HTML
	 * @param string $result
	 * @return string
	 */
	function strip_empty_tags($result)
	{
		$regexps = array(
			'~<(\w+)\b[^\>]*>\s*</\\1>~',
			'~<\w+\s*/>~'
		);

		do
		{
			$string = $result;
			$result = preg_replace($regexps, '', $string);
		}
		while ($result != $string);

		return $result;
	}

	/**
	 * Close open HTML tags
	 * @param string $html
	 * @return string
	 */
	function closetags($html)
	{
		// put all opened tags into an array
		preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $html, $result);
		$openedtags = $result[1];

		// put all closed tags into an array
		preg_match_all("#</([a-z]+)>#iU", $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);

		// all tags are closed
		if (count($closedtags) == $len_opened)
		{
			return $html;
		}

		$openedtags = array_reverse($openedtags);
		// close tags
		for ($i = 0; $i < $len_opened; $i++)
		{
			if (!in_array($openedtags[$i], $closedtags))
			{
				$html .= "</" . $openedtags[$i] . ">";
			}
			else
			{
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $html;
	}

	/**
	 * Cleanup unwanted HTML
	 * @param string $text
	 * @return string
	 */
	function clean_html($text)
	{
		$text = preg_replace(
				array(
			// Remove invisible content
			'@<head[^>]*?>.*?</head>@siu',
			'@<style[^>]*?>.*?</style>@siu',
			'@<script[^>]*?.*?</script>@siu',
			'@<object[^>]*?.*?</object>@siu',
			'@<embed[^>]*?.*?</embed>@siu',
			'@<applet[^>]*?.*?</applet>@siu',
			'@<noframes[^>]*?.*?</noframes>@siu',
			'@<noscript[^>]*?.*?</noscript>@siu',
			'@<noembed[^>]*?.*?</noembed>@siu',
			// Remove headings
			'@<h1>.+?</h1>@siu',
			'@<h2>.+?</h2>@siu',
			'@<h3>.+?</h3>@siu',
			// Add line breaks before and after blocks
			'@</?((address)|(blockquote)|(center)|(del))@iu',
			'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
			'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
			'@</?((table)|(th)|(td)|(caption))@iu',
			'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
			'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
			'@</?((frameset)|(frame)|(iframe))@iu',
				), array(
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			' ',
			"$0",
			"$0",
			"$0",
			"$0",
			"$0",
			"$0",
			"$0",
			"$0",), $text);

		// Exclude some html tags here
		$text = strip_tags($text, '<p><br><em><i><a>');
		$text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);

		// Cleaning tables
		$text = preg_replace('/(<[^>]+) border=".*?"/i', '$1', $text);
		$text = preg_replace('/(<[^>]+) align=".*?"/i', '$1', $text);
		$text = preg_replace('/(<[^>]+) cellpadding=".*?"/i', '$1', $text);
		$text = preg_replace('/(<[^>]+) cellspacing=".*?"/i', '$1', $text);
		$text = preg_replace('/<td>\\s|&nbsp;+<\/td>/', '', $text);
		$text = str_replace('<td></td>', '', $text);
		$text = str_replace('<td><td>', '', $text);
		$text = str_replace('<tr></tr>', '', $text);
		$text = str_replace('<br/>', '<br>', $text);
		return $this->clean_title($text);
	}

	/**
	 * Set quote to html enitity
	 */
	function ent_quotes($text)
	{
		return str_replace("'", "&#39;", $text);
	}

	/**
	 * cleanup title
	 * @param string $text
	 * @return string
	 */
	function clean_title($text)
	{

		$text = str_replace("è", "&egrave;", $text);
		$text = str_replace("ë", "&euml;", $text);
		$text = str_replace("é", "&eacute;", $text);
		$text = str_replace("ï", "&iuml;", $text);
		$text = str_replace("�", "", $text);

		return $this->ent_quotes($text);
	}

	/**
	 * Elegant word wrap
	 * @param string $str
	 * @param int $n
	 * @param string $end_char
	 * @return string
	 */
	function character_limiter($str, $n = 300, $end_char = '...')
	{
		if (strlen($str) < $n)
		{
			return $str;
		}

		$str = preg_replace("/\s+/", ' ', str_replace(array(
			"\r\n",
			"\r",
			"\n"), ' ', $str));

		if (strlen($str) <= $n)
		{
			return $str;
		}

		$out = "";
		foreach (explode(' ', trim($str)) as $val)
		{
			$out .= $val . ' ';

			if (strlen($out) >= $n)
			{
				$out = trim($out);
				return (strlen($out) == strlen($str)) ? $out : $out . $end_char;
			}
		}
	}

	/**
	 * Build form selectbox
	 * Borrowed from CodeIgniter
	 * @param string $name
	 * @param array $options
	 * @param array $selected
	 * @param string $extra
	 * @return string
	 */
	function form_dropdown($name = '', $options = array(), $selected = array(), $extra = '')
	{
		if (!is_array($selected))
		{
			$selected = array(
				$selected);
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0)
		{
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$name]))
			{
				$selected = array(
					$_POST[$name]);
			}
		}

		if ($extra != '')
		{
			$extra = ' ' . $extra;
		}
		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === false) ? ' multiple="multiple"' : '';

		$form = '<select name="' . $name . '"' . $extra . $multiple . ">\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val) && !empty($val))
			{
				$form .= '<optgroup label="' . $key . '">' . "\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';

					$form .= '<option value="' . $optgroup_key . '"' . $sel . '>' . (string) $optgroup_val . "</option>\n";
				}

				$form .= '</optgroup>' . "\n";
			}
			else
			{
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

				$form .= '<option value="' . $key . '"' . $sel . '>' . (string) $val . "</option>\n";
			}
		}

		$form .= '</select>';

		return $form;
	}

	/**
	 * Validate and cleanup title
	 * Uses default censor_text from phpBB with some added blacklist words for CMS
	 * @param string $title
	 * @return string
	 */
	function phpbb_censor_title($title)
	{
		$disallowed = array(
			'index',
			'home',
			'homepage',
			'search',
			'test',
		);
		if (in_array(strtolower(trim($title)), $disallowed))
		{
			return false;
		}
		return trim(censor_text($title));
	}

}
