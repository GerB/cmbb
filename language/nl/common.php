<?php

/**
 *
 * cmBB [Dutch]
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACL_U_CMBB_POST_ARTICLE'	 => 'Kan cmBB artikelen schrijven',
	'ARTICLES'					 => 'Artikelen',
	'ARTICLES_TOTAL'			 => 'Aantal artikelen',
	'ARTICLE_HIDDEN_WARNING'	 => 'Dit artikel is verborgen en daarom alleen toegankelijk voor moderators',
	'BACK'						 => 'Terug',
	'CATEGORY'					 => 'Categorie',
	'CMBB_UPLOAD_BROWSE'		 => 'Of bladeren',
	'CMBB_UPLOAD_DRAG'			 => 'Sleep je bestanden naar dit vak',
	'CMBB_UPLOAD_EXPLAIN'		 => 'Upload bestanden via de uploadbox hieronder. <br /> Toegestande bestandstypen: ',
	'COMMENTS'					 => 'Plaats en bekijk reacties',
	'COMMENTS_DISABLED'			 => 'Reacties uitgeschakeld',
	'CONTENT'					 => 'Artikel inhoud',
	'DELETE_ARTICLE'			 => 'Verberg artikel',
	'EDIT_ARTICLE'				 => 'Bewerk artikel',
	'ERROR_MUCH_REMOVED'		 => 'Je hebt vrij veel inhoud uit het artikel verwijderd. Dit kan door een fout of door misbruik komen. Uit voorzorg is het artikel niet opgeslagen.',
	'ERROR_TITLE'				 => 'De opgegeven titel is niet toegestaan.',
	'FEATURED_IMG'				 => 'Uitgelichte afbeelding',
	'LOG_ARTICLE_VISIBILLITY'	 => 'Zichtbaarheid artikel aangepast',
	'NEW_ARTICLE'				 => 'Nieuw artikel',
	'NO_HIDDEN'					 => 'Geen verborgen artikelen',
	'NO_REACTIONS_ARTICLE'		 => 'Reacties uitschakelen <small>(Reacties kunnen reeds uitgeschakeld zijn via categorie instellingen)</small>',
	'READ_MORE'					 => 'Lees meer...',
	'RESTORE_ARTICLE'			 => 'Herstel artikel',
	'SEARCH_USER_ARTICLES'		 => 'Zoek gebruikers artikelen',
	'SHOW_HIDDEN'				 => 'Toon verborgen artikelen',
	'TITLE'						 => 'Titel',
	'USE_AVATAR'				 => '-gebruik avatar-',
	'WELCOME_USER'				 => 'Hallo %s!',
		));
