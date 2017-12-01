<?php

/**
 *
 * cmBB [English]
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
	'ACL_U_CMBB_POST_ARTICLE'	 => 'Peut poster des articles cmBB',
	'ARTICLE_HIDDEN_WARNING'	 => 'Cet article est caché et par conséquent seulement accessible aux modérateurs',
	'ARTICLES'					 => 'Articles',
	'ARTICLES_TOTAL'			 => 'Total des articles',
	'BACK'						 => 'Retour',
	'CATEGORY'					 => 'Catégorie',
	'CMBB_UPLOAD_BROWSE'		 => 'Ou parcourir',
	'CMBB_UPLOAD_DRAG'			 => 'Glissez et déposez vos fichiers ici',
	'CMBB_UPLOAD_EXPLAIN'		 => 'Chargez des fichiers via la boîte de chargement ci-dessous. <br /> Les types de fichiers autorisés sont : ',
	'COMMENTS'					 => 'Voir/Poster commentaires',
	'COMMENTS_DISABLED'			 => 'Commentaires désactivés',
	'CONTENT'					 => 'Contenu de l’article',
	'DELETE_ARTICLE'			 => 'Cacher l’article',
	'EDIT_ARTICLE'				 => 'Editer l’article',
	'ERROR_MUCH_REMOVED'		 => 'Vous avez retiré beaucoup de cet article. Cela peut être abusif ou une simple erreur de l’utilisateur. Les données ne sont PAS stockées.',
	'ERROR_TITLE'				 => 'Le titre proposé n’est pas autorisé.',
	'FEATURED_IMG'				 => 'Image d’illustration',
	'LOG_ARTICLE_VISIBILLITY'	 => 'A changé la visibilité de l’article',
	'NEW_ARTICLE'				 => 'Nouvel article',
	'NO_HIDDEN'					 => 'Pas d’articles cachés',
	'NO_REACTIONS_ARTICLE'		 => 'Désactiver les commentaires <small>(les commentaires peuvent être déjà désactivés à travers la configuration de la catégorie)</small>',
	'READ_MORE'					 => 'Lire plus...',
	'RESTORE_ARTICLE'			 => 'Restaurer l’article',
	'SEARCH_USER_ARTICLES'		 => 'Rechercher les articles de l’utilisateur',
	'SHOW_HIDDEN'				 => 'Afficher les articles cachés',
	'TITLE'						 => 'Titre',
	'USE_AVATAR'				 => '-utiliser l’avatar-',
	'WELCOME_USER'				 => 'Bonjour %s!',
		));
