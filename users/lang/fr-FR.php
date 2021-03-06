<?php
/*
TO CREATE A NEW LANGUAGE, COPY THE en-us.php to your own localization code name.
We are going to keep these files in the iso xx-xx format because that will also
allow us to autoformat numbers on the sites.

PLEASE put your name somewhere at the top of the language file so we can get in touch with
you to update it and thank you for your hard work!

PLEASE NOTE: DO NOT ADD RANDOM KEYS in the middle of the translations.  In order to make it easier to tell what language keys are missing, from this point forward, we are going to add all new language keys at the BOTTOM of this file. The number of lines in your language file will tell you which keys still need to be translated.  If you have questions please ask on the forums or on Discord.

UserSpice
An Open Source PHP User Management System
by the UserSpice Team at http://UserSpice.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
%m1% - Dymamic markers which are replaced at run time by the relevant index.
*/

$lang = array();
//important strings
//You defiitely want to customize these for your language
$lang = array_merge($lang,array(
"THIS_LANGUAGE"	=>"French",
"THIS_CODE"			=>"en-US",
"MISSING_TEXT"	=>"Texte manquant",
));

//Database Menus
$lang = array_merge($lang,array(
"MENU_HOME"			=> "Accueil",
"MENU_HELP"			=> "Aide",
"MENU_ACCOUNT"	=> "Compte",
"MENU_DASH"			=> "Tableau de bord administrateur",
"MENU_USER_MGR"	=> "Gestion des utilisateurs",
"MENU_PAGE_MGR"	=> "Gestion de page",
"MENU_PERM_MGR"	=> "Gestion des autorisations",
"MENU_MSGS_MGR"	=> "Gestionnaire de messages",
"MENU_LOGS_MGR"	=> "Journaux syst??me",
"MENU_LOGOUT"		=> "Se d??connecter",
));

// Signup
$lang = array_merge($lang,array(
	"SIGNUP_TEXT"					=> "S'inscrire",
	"SIGNUP_BUTTONTEXT"		=> "S'inscrire",
	"SIGNUP_AUDITTEXT"		=> "Inscrite",
	));

// Signin
$lang = array_merge($lang,array(
	"SIGNIN_FAIL"				=> "** ??CHEC DE LA CONNEXION **",
	"SIGNIN_PLEASE_CHK" => "Veuillez v??rifier vos identifiant et mot de passe puis r??-essayer",
	"SIGNIN_UORE"				=> "Nom d'utilisateur ou email",
	"SIGNIN_PASS"				=> "Mot de passe",
	"SIGNIN_TITLE"			=> "Veuillez vous connecter",
	"SIGNIN_TEXT"				=> "s'identifier",
	"SIGNOUT_TEXT"			=> "se d??connecter",
	"SIGNIN_BUTTONTEXT"	=> "S'identifier",
	"SIGNIN_REMEMBER"		=> "souviens-toi de moi",
	"SIGNIN_AUDITTEXT"	=> "connect??",
	"SIGNIN_FORGOTPASS"	=>"Mot de passe oubli??",
	"SIGNOUT_AUDITTEXT"	=> "D??connect??",
	));

// Account Page
$lang = array_merge($lang,array(
	"ACCT_EDIT"					=> "Editer infos compte",
	"ACCT_2FA"					=> "G??rer l'authentification ?? 2 facteurs",
	"ACCT_SESS"					=> "G??rer les sessions",
	"ACCT_HOME"					=> "Compte Accueil",
	"ACCT_SINCE"				=> "Membre depuis",
	"ACCT_LOGINS"				=> "Nombre de connexions",
	"ACCT_SESSIONS"			=> "Nombre de sessions actives",
	"ACCT_MNG_SES"			=> "Cliquez sur le bouton G??rer les sessions dans la barre lat??rale gauche pour plus d'informations.",
	));

	//General Terms
	$lang = array_merge($lang,array(
		"GEN_ENABLED"			=> "Activ??",
		"GEN_DISABLED"		=> "D??sactiv??",
		"GEN_ENABLE"			=> "Activer",
		"GEN_DISABLE"			=> "D??sactiver",
		"GEN_NO"					=> "Non",
		"GEN_YES"					=> "Oui",
		"GEN_MIN"					=> "min",
		"GEN_MAX"					=> "max",
		"GEN_CHAR"				=> "char", //as in characters
		"GEN_SUBMIT"			=> "Soumettre",
		"GEN_MANAGE"			=> "Manage",
		"GEN_VERIFY"			=> "V??rifier",
		"GEN_SESSION"			=> "Session",
		"GEN_SESSIONS"		=> "Sessions",
		"GEN_EMAIL"				=> "Email",
		"GEN_FNAME"				=> "Pr??nom",
		"GEN_LNAME"				=> "nom de famille",
		"GEN_UNAME"				=> "Nom d'utilisateur",
		"GEN_PASS"				=> "mot de passe",
		"GEN_MSG"					=> "Message",
		"GEN_TODAY"				=> "aujourd'hui",
		"GEN_CLOSE"				=> "Fermer",
		"GEN_CANCEL"			=> "Annuler",
		"GEN_CHECK"				=> "[ Verifier/non v??rifi??s  tous ]",
		"GEN_WITH"				=> "Avec",
		"GEN_UPDATED"			=> "Mis ?? jour",
		"GEN_UPDATE"			=> "Mise ?? jour",
		"GEN_BY"					=> "Par",
		"GEN_FUNCTIONS"		=> "Fonctions",
		"GEN_NUMBER"			=> "Nombre",
		"GEN_NUMBERS"			=> "Nombres",
		"GEN_INFO"				=> "Information",
		"GEN_REC"					=> "Enregistr??",
		"GEN_DEL"					=> "Effac??",
		"GEN_NOT_AVAIL"		=> "Non disponible",
		"GEN_AVAIL"				=> "Disponible",
		"GEN_BACK"				=> "Retour",
		"GEN_RESET"				=> "Reinitialiser",
		"GEN_REQ"					=> "Requis",
		"GEN_AND"					=> "et",
		"GEN_SAME"				=> "doit ??tre le m??me",
		));

//validation class
	$lang = array_merge($lang,array(
		"VAL_SAME"				=> "doit ??tre le m??me",
		"VAL_EXISTS"			=> "existe d??j??. Merci d'en choisir un autre",
		"VAL_DB"					=> "erreur de la base de donn??es",
		"VAL_NUM"					=> "doit ??tre un nombre",
		"VAL_INT"					=> "doit ??tre un nombre entier",
		"VAL_EMAIL"				=> "doit ??tre une adresse email valide",
		"VAL_NO_EMAIL"		=> "ne peut pas ??tre une adresse email",
		"VAL_SERVER"			=> "Doit ??tre un serveur valide",
		"VAL_LESS"				=> "Doit ??tre inf??rieur ??",
		"VAL_GREAT"				=> "Doit ??tre sup??rieur ??",
		"VAL_LESS_EQ"			=> "Doit ??tre inf??rieur ou ??gal ??",
		"VAL_GREAT_EQ"		=> "Doit ??tre sup??rieur ou ??gal ??",
		"VAL_NOT_EQ"			=> "Ne doit pas ??tre ??gal ??",
		"VAL_EQ"					=> "Doit ??tre ??gal ??",
		"VAL_TZ"					=> "doit ??tre un nom de fuseau horaire valide",
		"VAL_MUST"				=> "Doit ??tre",
		"VAL_MUST_LIST"		=> "Doit ??tre l'un des suivants",
		"VAL_TIME"				=> "Doit ??tre une heure valide",
		"VAL_SEL"					=> "n'est pas une s??lection valide",
		"VAL_NA_PHONE"		=> "doit ??tre un num??ro de t??l??phone nord-am??ricain valide",
	));

		//Time
	$lang = array_merge($lang,array(
		"T_YEARS"			=> "Ann??es",
		"T_YEAR"			=> "Ann??e",
		"T_MONTHS"		=> "Mois",
		"T_MONTH"			=> "Mois",
		"T_WEEKS"			=> "Semaines",
		"T_WEEK"			=> "Semaine",
		"T_DAYS"			=> "Jours",
		"T_DAY"				=> "Jour",
		"T_HOURS"			=> "Heures",
		"T_HOUR"			=> "Heure",
		"T_MINUTES"		=> "Minutes",
		"T_MINUTE"		=> "Minute",
		"T_SECONDS"		=> "Secondes",
		"T_SECOND"		=> "Seconde",
		));


		//Passwords
	$lang = array_merge($lang,array(
		"PW_NEW"		=> "Nouveau mot de passe",
		"PW_OLD"		=> "Ancien mot de passe",
		"PW_CONF"		=> "Confirmer mot de passe",
		"PW_RESET"	=> "R??initialiser mot de passe",
		"PW_UPD"		=> "Mot de passe mis ?? jour",
		"PW_SHOULD"	=> "Mot de passe devrait...",
		"PW_SHOW"		=> "Montrer le mot de passe",
		"PW_SHOWS"	=> "Montrer les mots de passe",
		));


		//Join
	$lang = array_merge($lang,array(
		"JOIN_SUC"			=> "Bienvenue ",
		"JOIN_THANKS"		=> "Merci de votre inscription!",
		"JOIN_HAVE"			=> "Avoir au moins ",
		"JOIN_CAP"			=> " lettre capitale",
		"JOIN_TWICE"		=> "taper deux fois correctement",
		"JOIN_CLOSED"		=> "Inscription malheureusement d??sactiv??e en ce moment. SVP contacter l'administrateur du site si vous avez des questions ou des probl??mes.",
		"JOIN_TC"				=> "Utilisateur termes et conditions",
		"JOIN_ACCEPTTC" => "J'accepte les termes et conditions de l'utilisateur",
		"JOIN_CHANGED"	=> "Nos termes ont ??t?? modifi??s",
		"JOIN_ACCEPT" 	=> "Accepter les termes et conditions utilisateur et continuer",
		));

		//Sessions
	$lang = array_merge($lang,array(
		"SESS_SUC"	=> "tu?? avec succ??s ",
		));

		//Messages
	$lang = array_merge($lang,array(
		"MSG_SENT"			=> "Message envoy??!",
		"MSG_MASS"			=> "Votre message collectif a ??t?? envoy??!",
		"MSG_NEW"				=> "Nouveau message",
		"MSG_NEW_MASS"	=> "Nouveau message collectif",
		"MSG_CONV"			=> "Conversations",
		"MSG_NO_CONV"		=> "Pas de conversations",
		"MSG_NO_ARC"		=> "Pas de conversation",
		"MSG_QUEST"			=> "Envoyer un E mail de confirmation si activ???",
		"MSG_ARC"				=> "threads archiv??s",
		"MSG_VIEW_ARC"	=> "Voir les threads archiv??s",
		"MSG_SETTINGS"  => "Message param??tres",
		"MSG_READ"			=> "Lire",
		"MSG_BODY"			=> "Contenu",
		"MSG_SUB"				=> "Sujet",
		"MSG_DEL"				=> "D??livr??",
		"MSG_REPLY"			=> "R??ponse",
		"MSG_QUICK"			=> "R??ponse rapide",
		"MSG_SELECT"		=> "S??lectionner un utilisateur",
		"MSG_UNKN"			=> "R??cipient inconnu",
		"MSG_NOTIF"			=> "Message Email Notifications",
		"MSG_BLANK"			=> "Le message doit avoir un contenu",
		"MSG_MODAL"			=> "Cliquez ici ou appuyez sur Alt + R pour vous concentrer sur cette case OU appuyez sur Maj + R pour ouvrir le panneau de r??ponse d??velopp??

!",
		"MSG_ARCHIVE_SUCCESSFUL"        => "Vous avez archiv?? avec succ??s %m1% discussions",
		"MSG_UNARCHIVE_SUCCESSFUL"      => "Vous avez d??sarchiv?? %m1% threads avec succ??s",
		"MSG_DELETE_SUCCESSFUL"         => "Vous avez supprim?? avec succ??s %m1% discussions",
		"USER_MESSAGE_EXEMPT"         			=> "L'utilisateur est %m1% exempt?? de messages.",
		"MSG_MK_READ"		=> "lu",
		"MSG_MK_UNREAD"	=> "Non lu",
		"MSG_ARC_THR"		=> "Archiver les threads s??lectionn??s",
		"MSG_UN_THR"		=> "D??sarchiver les threads s??lectionn??s",
		"MSG_DEL_THR"		=> "Supprimer les threads s??lectionn??s",
		"MSG_SEND"			=> "Envoyer le message",
		));

	//2 Factor Authentication
	$lang = array_merge($lang,array(
		"2FA"				=> "Authentification ?? 2 facteurs",
		"2FA_CONF"	=> "Etes vous s??r de vouloir d??sactiver 2FA ? Votre compte ne sera plus prot??g??.",
		"2FA_SCAN"	=> "Scan this QR code avec votre application d'authentification ou rentrer la cl??",
		"2FA_THEN"	=> "Puis entrer de vos passes cl??s uniques ici",
		"2FA_FAIL"	=> "Il y a eu un probl??me pour v??rifier 2FA. SVOP voir Internet ou contacter l'assistance.",
		"2FA_CODE"	=> "2FA Code",
		"2FA_EXP"		=> "Expir?? 1 empreinte digitale",
		"2FA_EXPD"	=> "Expir??",
		"2FA_EXPS"	=> "En cours d'expiration",
		"2FA_ACTIVE"=> "sessions actives",
		"2FA_NOT_FN"=> "Pas d'empreinte trouv??e",
		"2FA_FP"		=> "Empreintes digitales",
		"2FA_NP"		=> "<strong>Echec login</strong> Double facteur d'authentification non pr??sent. SVP essayer de nouveau.",
		"2FA_INV"		=> "<strong>Echec login</strong> double facteur d'authentification invalide. SVP essayer de nouveau.",
		"2FA_FATAL"	=> "<strong>Erreur fatale</strong> SVP contacter l'administrateur du syst??me.",
		));

	//Redirect Messages - These get a plus between each word
	$lang = array_merge($lang,array(
		"REDIR_2FA"						=> "D??sol??.Deux+facteurs+n'est+pas+activ??+en+ce+moment",
		"REDIR_2FA_EN"				=> "2+facteur+authentification+Activ??e",
		"REDIR_2FA_DIS"				=> "2+facteur+authentification+Desactiv??e",
		"REDIR_2FA_VER"				=> "2+facteur+authentification+v??rifi??e+et+activ??e",
		"REDIR_SOM_TING_WONG" => "erreur+SVP+essayer+de+nouveau.",
		"REDIR_MSG_NOEX"			=> "Ce+thread+ne+vous+appartient+pas+ou+n'existe+pas.",
		"REDIR_UN_ONCE"				=> "Le+nom+d'utilisateur+a+d??j??+??t??+chang??.",
		"REDIR_EM_SUCC"				=> "Email+Mise+??+jour+r??ussie",
		));

	//Emails
	$lang = array_merge($lang,array(
		"EML_CONF"			=> "Confirmer Email",
		"EML_VER"				=> "V??rifiez votre email",
		"EML_CHK"				=> "Demande d'email re??ue. SVP voir vos emails pour v??rification. Veillez ?? consulter vos dossiers Spam et Junk lorsque le lien de v??rification expire ",
		"EML_MAT"				=> "Votre Email n'??tait pas le m??me.",
		"EML_HELLO"			=> "Hello de ",
		"EML_HI"				=> "Bonjour ",
		"EML_AD_HAS"		=> "Un administrateur a chang?? votre mot de passe.",
		"EML_AC_HAS"		=> "Un administrateur a cr???? votre compte.",
		"EML_REQ"				=> "Vous devrez ??tablir votre mot de passe en utilisant le lien ci dessus.",
		"EML_EXP"				=> "SVP , notez , le lien mot de passe expirer dans ",
		"EML_VER_EXP"		=> "SVP , notez, le lien de v??rification expire dans ",
		"EML_CLICK"			=> "Cliquer ici pour connection.",
		"EML_REC"				=> "Il est recommand?? de changer de mot de passe apr??s connection.",
		"EML_MSG"				=> "Vous avez un nouveau message de",
		"EML_REPLY"			=> "Cliquez ici pour r??pondre ou voir le thread",
		"EML_WHY"				=> "Vous recevez cet Email parce qu'une demande de renouvellement de mot de passe a ??t?? faite. Si ce n'??tait pas vous , vous pouvez ignorer cet Email.",
		"EML_HOW"				=> "Si c'??tait vous , cliquez sur le lien ci dessous pour poursuivre le processus de changement de mot de passe.",
		"EML_EML"				=> "Une demande de modification de votre email a ??t?? faite ?? partir de votre compte d'utilisateur.",
		"EML_VER_EML"		=> "Merci de votre inscription. une fois que vous aurez v??rifi?? votre adresse mail vous serez pr??t ?? vous connecter ! SVP cliquez sur le lien ci dessous pour v??rifier votre adresse mail.",

		));

		//Verification
		$lang = array_merge($lang,array(
			"VER_SUC"			=> "Votre Email a ??t?? v??rifi??!",
			"VER_FAIL"		=> "Il nous est impossible de v??rifier votre compte . SVP essayez de nouveau.",
			"VER_RESEND"	=> "renvoyer un e mail de v??rification",
			"VER_AGAIN"		=> "entrer votre adresse mail et essayez de nouveau",
			"VER_PAGE"		=> "<li>V??rifiez votre email et cliquez sur le lien qui vous est envoy??</li><li>Termin??</li>",
			"VER_RES_SUC" => "<p>Votre lien de v??rification a ??t?? envoy?? ?? votre adresse e-mail.</p><p>Cliquez sur le lien dans l'e-mail pour terminer la v??rification. Assurez-vous de v??rifier votre dossier de courrier ind??sirable si l'email n'est pas dans votre bo??te de r??ception.</p><p>Les liens de v??rification ne sont valables que pour ",
			"VER_OOPS"		=> "Ooops...quelque chose n'a pas fonctionn??, peut-??tre un lien ancien sur lequel vous avez cliqu??. Cliquez ci dessous pour essayer de nouveau",
			"VER_RESET"		=> "Votre mot de passe a ??t?? chang??!",
			"VER_INS"			=> "<li>Entrez votre adresse mail et cliquez sur r??initialiser</li> <li> regardez vos emails et cliquez sur le lien qui vous a ??t?? envoy??.</li><li>Suivez les instructions sur l'??cran</li>",
			"VER_SENT"		=> "<p>Votre lien pour modifier votre mot de passe a ??t?? envoy?? ?? votre adresse mail.</p>
			    							<p>Cliquez sur le lien dans l'email pour r??initialiser votre mot de passe. Assurez-vous de v??rifier votre dossier de courrier ind??sirable si l'email n'est pas dans votre bo??te de r??ception.</p><pLes liens de r??initialisation ne sont valables que pour ",
			"VER_PLEASE"	=> "SVP Modifiez votre mot de passe",
			));

	//User Settings
	$lang = array_merge($lang,array(
		"SET_PIN"				=> "r??initialiser le PIN",
		"SET_WHY"				=> "Pourquoi je ne peux pas changer ??a?",
		"SET_PW_MATCH"	=> "Doit ??tre semblable au nouveau mot de passe",

		"SET_PIN_NEXT"	=> "Vous pourrez changer le PIN la prochaine fois que vous ferez une v??rification",
		"SET_UPDATE"		=> "Mettre ?? jour vos donn??es d'utilisateur",
		"SET_NOCHANGE"	=> "L'administrateur a d??sactiv?? la capacit?? de changer de nom d'utilisateur.",
		"SET_ONECHANGE"	=> "L'administrateur n'a autoris?? qu'un changement de nom d'utilisateur et vous avez d??j?? utilis?? cette possibilit??.",

		"SET_GRAVITAR"	=> "<strong>Voulez vous changer votre photo? </strong><br> Visitez <a href='https://en.gravatar.com/'https://en.gravatar.com/</a> et cr??ez votre compte avec le m??me Email que vous utilisez sur ce site . Cela fonctionne pour des millions de sites. C'est rapide et facile! utilis??e sur ce site",

		"SET_NOTE1"			=> "<p><strong>SVP notez</strong> il y a une demande en attente pour mettre ?? jour votre Email ??",

		"SET_NOTE2"			=> ".</p><p>SVP utilisez l'Email de v??rification pour r??pondre ?? cette demande.</p>
		<p>Si vous avez besoin d'un nouvel Email de v??rification, SVP re-entrez l'Email ci dessus et faites de nouveau la demande.</p>",

		"SET_PW_REQ" 		=> "N??cessaire pour changer de mot de passe, Email, or r??initialiser le PIN",
		"SET_PW_REQI" 	=> "N??cessaire pour changer de mot de passe",

		));

	//Errors
	$lang = array_merge($lang,array(
		"ERR_FAIL_ACT"		=> "Echec pour fermer la session en cours , Erreur: ",
		"ERR_EMAIL"				=> "Email non envoy?? pour cause d'erreur. SVP contacter l'administrateur du site.",
		"ERR_EM_DB"				=> "Cet email n'existe pas dans votre base de donn??es",
		"ERR_TC"					=> "SVP lire et accepter les termes et conditions",
		"ERR_CAP"					=> "Vous avez ??chou?? au test captcha, robot!",
		"ERR_PW_SAME"			=> "Votre ancien mot de passe ne peut ??tre le m??me que le nouveau",
		"ERR_PW_FAIL"			=> "La v??rification de mot de passe en cours a ??chou?? . Echec de la mise ?? jour. SVP essayer de nouveau.",
		"ERR_GOOG"				=> "<strong>REMARQUE:</strong> Si vous ??tes connect?? avec votre compte google / facebook, utilisez le lien mot de passe oubli?? pour changer de mot de passe ... sauf si vous ??tes dou?? pour quesser",
		"ERR_EM_VER"			=> "La v??rification dEmail n'est pas activ??e. SVP Contacter l'administrateur syst??me.",
		"ERR_EMAIL_STR"		=> "Quelque chose est bizarre. SVP re-v??rifiez notre Email.Nous sommes d??sol??s pour les inconv??nients",

		));

	//Maintenance Page
	$lang = array_merge($lang,array(
		"MAINT_HEAD"		=> "Nous serons bient??t de retour!",
		"MAINT_MSG"			=> "D??sol?? pour le d??sagr??ment mais nous effectuons actuellement des op??rations de maintenance .<br> Nous reviendrons tr??s rapidement en ligne.!",
		"MAINT_BAN"			=> "D??sol?? , vous avez ??t?? interdit . Si vous pensez que c'est une erreur , SVP contactez l'administrateur.",
		"MAINT_TOK"			=> "Il y avait une erreur dans votre formulaire . SVP retournez en arri??re et essayez de nouveau. SVP notez que soumettre le formulaire en rafra??chissant la page peut causer une erreur. Si cela continue de se produire , SVP contactez l'administrateur.",
		"MAINT_OPEN"		=> "Une source ouverte PHP utilisateur management programme.",
		"MAINT_PLEASE"	=> "Vous avez correctement install?? UserSpice! <br> Pour consulter notre documentation de mise en route, rendez-vous sur"
		));

		//dataTables Added in 4.4.08
		//NOTE: do not change the words like _START_ between the two _ symbols!
		$lang = array_merge($lang,array(
		"DAT_SEARCH"    => "Chercher",
		"DAT_FIRST"     => "Premier",
		"DAT_LAST"      => "Dernier",
		"DAT_NEXT"      => "Suivant",
		"DAT_PREV"      => "Pr??c??dent",
		"DAT_NODATA"        => "Pas de donn??es disponibles en tableau",
		"DAT_INFO"          => "Affichage _START_ ?? _END_ du _TOTAL_ entr??es",
		"DAT_ZERO"          => "Affichage ?? ?? ?? de ?? entr??es",
		"DAT_FILTERED"      => "(Filtr?? de _MAX_ total entr??es)",
		"DAT_MENU_LENG"     => "affichage _MENU_ entr??es",
		"DAT_LOADING"       => "Chargement...",
		"DAT_PROCESS"       => "Traitement...",
		"DAT_NO_REC"        => "Pas d'enregistrements correspondant trouv??s",
		"DAT_ASC"           => "activer pour trier ordre ascendant",
		"DAT_DESC"          => "Activer pour trier ordre descendant",
		));


///////////////////////////////////////////////////////////////

//Backend Translations for UserSpice
$lang = array_merge($lang,array(
"BE_DASH"    			=> "Tableau interface",
"BE_SETTINGS"     => "Param??tres",
"BE_GEN"					=> "General",
"BE_REG"					=> "Inscription",
"BE_CUS"					=> "Param??tres personnalis??s",
"BE_DASH_ACC"			=> "Acc??s au tableau interface",
"BE_TOOLS"				=> "Outils",
"BE_BACKUP"				=> "Sauvegarde",
"BE_UPDATE"				=> "Mises ?? jour",
"BE_CRON"				  => "Cron planifi??es",
"BE_IP"				  	=> "IP Manager",
));



//LEAVE THIS LINE AT THE BOTTOM.  It allows users/lang to override these keys
if(file_exists($abs_us_root.$us_url_root."usersc/lang/".$lang["THIS_CODE"].".php")){
	include($abs_us_root.$us_url_root."usersc/lang/".$lang["THIS_CODE"].".php");
}
?>
