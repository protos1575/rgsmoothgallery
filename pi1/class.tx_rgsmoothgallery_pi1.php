<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Georg Ringer <http://www.just2b.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 * Plugin 'SmoothGallery' for the 'rgsmoothgallery' extension.
 *
 * @author	Georg Ringer <http://www.just2b.com>
 * @package	TYPO3
 * @subpackage	tx_rgsmoothgallery
 */
class tx_rgsmoothgallery_pi1 extends tslib_pibase {
	var $prefixId = 'tx_rgsmoothgallery_pi1'; // Same as class name
	var $scriptRelPath = 'pi1/class.tx_rgsmoothgallery_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey = 'rgsmoothgallery'; // The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * Just some intialization, mainly reading the settings in the flexforms
	 *
	 * @param	array		$conf: The PlugIn configuration
	 */
	function init($conf) {
		$this->conf = $conf; // Storing configuration as a member var
		$this->pi_loadLL(); // Loading language-labels
		$this->pi_setPiVarDefaults(); // Set default piVars from TS
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
		

		// Template code
		$this->templateCode = $this->cObj->fileResource( $this->conf['templateFile'] );
		$this->config['count'] = 0;
		
		// configuration flexforms
		$this->config['mode'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'mode', 'sDEF' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'mode', 'sDEF' ) : $this->conf['mode'];
		$this->config['duration'] = intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'time', 'sDEF' ) ) ? intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'time', 'sDEF' ) ) : intval( $this->conf['duration'] );
		$this->config['startingpoint'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'startingpoint', 'sDEF' ) ? trim( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'startingpoint', 'sDEF' ) ) : trim( $this->conf['startingpoint'] );
		$pid = $this->conf['startingpointrecords'] ? $this->conf['startingpointrecords'] : $GLOBALS['TSFE']->id;
		$this->conf['startingpointrecords'] = $this->conf['startingpointrecords'] ? $this->conf['startingpointrecords'] : $pid;
		$this->config['startingpointrecords'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'startingpointrecords', 'sDEF' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'startingpointrecords', 'sDEF' ) :($this->conf['startingpointrecords']);
		$this->config['startingpointdam'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'startingpointdam', 'sDEF' );
		$this->config['startingpointdamcat'] = $this->getFlexform( 'sDEF', 'startingpointdamcat', 'startingpointdamcat' );
		$this->config['recursivedamcat'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'recursivedamcat', 'sDEF' );
		$this->config['text'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'text', 'sDEF' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'text', 'sDEF' ) : $this->conf['text'];
		$this->config['id'] = $this->cObj->data['uid'] . $this->conf['id'];
		
		// size of images, overwritten by flexforms
		$this->config['width'] = ($this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'width', 'sDEF' )) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'width', 'sDEF' ) : $this->conf['big.']['file.']['maxW'];
		#  if ($this->config['width'])  $this->conf['big.']['file.']['maxW'] = $this->config['width'];
		$this->config['height'] = ($this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'height', 'sDEF' )) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'height', 'sDEF' ) : $this->conf['big.']['file.']['maxH'];
		#  if ($this->config['height']) $this->conf['big.']['file.']['maxH'] = $this->config['height'];
		

		$this->config['heightGallery'] = intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'heightgallery', 'sDEF' ) ) ? intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'heightgallery', 'sDEF' ) ) : $this->conf['heightGallery'];
		$this->config['widthGallery'] = intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'widthgallery', 'sDEF' ) ) ? intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'widthgallery', 'sDEF' ) ) : $this->conf['widthGallery'];
		
		if (strpos( $this->config['width'], 'c' ) || strpos( $this->config['width'], 'm' ) || strpos( $this->config['height'], 'c' ) || strpos( $this->config['height'], 'm' )) {
			$this->conf['big.']['file.']['width'] = $this->config['width'];
			$this->conf['big.']['file.']['height'] = $this->config['height'];
			$this->conf['big2.']['file.']['10.']['file.']['width'] = $this->config['width'];
			$this->conf['big2.']['file.']['10.']['file.']['height'] = $this->config['height'];
			
			unset( $this->conf['big.']['file.']['maxW'] );
			unset( $this->conf['big.']['file.']['maxH'] );
			unset( $this->conf['big2.']['file.']['10.']['file.']['maxW'] );
			unset( $this->conf['big2.']['file.']['10.']['file.']['maxH'] );
		} else {
			if ($this->config['width']) {
				$this->conf['big.']['file.']['maxW'] = $this->config['width'];
				$this->conf['big2.']['file.']['10.']['file.']['maxW'] = $this->config['width'];
			}
			if ($this->config['height']) {
				$this->conf['big.']['file.']['maxH'] = $this->config['height'];
				$this->conf['big2.']['file.']['10.']['file.']['maxH'] = $this->config['height'];
			}
			if (!$this->config['heightGallery']) $this->config['heightGallery'] = $this->config['height'];
			if (!$this->config['widthGallery']) $this->config['widthGallery'] = $this->config['width'];      
		}
		
		// check starting point for missing slash
		if (substr($this->config['startingpoint'], -1) != '/') {
			$this->config['startingpoint'] = $this->config['startingpoint'].'/';
		}

		if (substr($this->config['startingpoint'], 0,1) == '/') {
			$size = strlen($this->config['startingpoint']);
			$this->config['startingpoint'] = substr($this->config['startingpoint'],1,$size-1);
		}

		
		/*
		 * Advanced settings
		 */
		$this->config['hideInfoPane'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'infopane', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'infopane', 'advanced' ) : $this->conf['hideInfoPane'];
		$this->config['thumbOpacity'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'thumbopacity', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'thumbopacity', 'advanced' ) : $this->conf['thumbOpacity'];
		$this->config['slideInfoZoneOpacity'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'slideinfozoneopacity', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'slideinfozoneopacity', 'advanced' ) : $this->conf['slideInfoZoneOpacity'];
		$this->config['thumbSpacing'] = intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'thumbspacing', 'advanced' ) ) ? intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'thumbspacing', 'advanced' ) ) : $this->conf['thumbSpacing'];
		
		$this->config['watermarks'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'watermark', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'watermark', 'advanced' ) : $this->conf['watermarks'];
		$this->config['limitImagesDisplayed'] = intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'limitImagesDisplayed', 'advanced' ) ) ? intval( $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'limitImagesDisplayed', 'advanced' ) ) : intval( $this->conf['limitImagesDisplayed'] );
		
		$this->config['lightbox'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'lightbox', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'lightbox', 'advanced' ) : $this->conf['lightbox'];
		$this->config['showThumbs'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'showThumbs', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'showThumbs', 'advanced' ) : $this->conf['showThumbs'];
		$this->config['showPlay'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'showPlay', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'showPlay', 'advanced' ) : $this->conf['showPlay'];
		$this->config['arrows'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'arrows', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'arrows', 'advanced' ) : $this->conf['arrows'];
		$this->config['advancedSettings'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'advancedsettings', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'advancedsettings', 'advanced' ) : $this->conf['advancedSettings'];
		$this->config['externalThumbs'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'externalthumbs', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'externalthumbs', 'advanced' ) : $this->conf['externalThumbs'];
		$this->config['externalControl'] = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'externalcontrol', 'advanced' ) ? $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], 'externalcontrol', 'advanced' ) : $this->conf['externalControl'];
		
		/* 
		 * Split characters from Extension Manager
		 */
		$tmp_confArr = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rgsmoothgallery'] );
		$tmp_confArr['splitRecord'] = ($tmp_confArr['splitRecord']) ? $tmp_confArr['splitRecord'] : '\n';
		$this->config['splitRecord'] = ($tmp_confArr['splitRecord'] == '\n') ? "\n" : $tmp_confArr['splitRecord'];
 
		$this->config['splitComment'] = ($tmp_confArr['splitComment']) ? $tmp_confArr['splitComment'] : '|';
		
		/*
		 * StdWrap options for every value from flexforms merged with TS to override it again with TS and to manipulate it with stdWrap things
		 */
		
		foreach ( $this->config as $key => $value ) {
			$this->config[$key] = $this->cObj->stdWrap( $value, $this->conf[$key . '.'] );
		}
	
	}
	
	/**
	 * The main method of the PlugIn
	 * for showing the SmoothGallery	 
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The gallery
	 */
	function main($content, $conf) {
		$this->init( $conf );
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 0; // Configuring so caching is expected. 
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin

		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
		  <script type="text/javascript" src="'.\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($this->extKey).'res/royalslider/jquery.royalslider.min.js"></script>
		  <link rel="stylesheet" type="text/css" href="'.\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($this->extKey).'res/royalslider/royalslider.css" />
		  <link rel="stylesheet" type="text/css" href="'.\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($this->extKey).'res/royalslider/skins/default/rs-default.css" />
		  <link rel="stylesheet" type="text/css" href="'.\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($this->extKey).'res/royalslider/royalSlider-custom.css" />
		  ';
		
		if ($this->conf['pathToJdgalleryJS'] == '') {
			return $this->pi_getLL( 'errorIncludeStatic' );
		} else {
			// get the needed js to load the gallery and to start it
			$content .= $this->getJs(
				$this->config['lightbox'],
				$this->config['showThumbs'],
				$this->config['arrows'],
				$this->config['duration'],
				$this->config['width'],
				$this->config['height'],
				$this->config['widthGallery'],
				$this->config['heightGallery'],        
				$this->config['advancedSettings'],
				$this->config['id'],
				$this->conf
			);
			
			// depending on the chosen settings the images come from different places
			$content .= $this->getImageDifferentPlaces( $this->config['limitImagesDisplayed'] );
			
			#return $this->pi_wrapInBaseClass($content);
			return '<div class="tx-rgsmoothgallery-pi1 rgsgnest' . $this->config['id'] . '">' . $content . '</div><div id="externalthumbs"></div>';
		}
	
	} # end main


	/**
	 * Just some divs needed for the gallery
	 *
	 * @param	string/int   $uniqueId: A unique ID to have more than 1 galleries on 1 page
	 * @return	The opened divs
	 */
	function beginGallery($uniqueId, $limitImages = 0) {
		if ($limitImages == 1) {
			$content = '<div class="royalSlider rsDefault" id="nbmpRs' . $uniqueId . '">';
		}
		else {
			$content = '<div class="royalSlider rsDefault" id="nbmpRs' . $uniqueId . '">';
		}
		$content .= '<!-- TTNEWS_YOUTUBE -->';

		if (is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraBeginGalleryHook'] )) {
			foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraBeginGalleryHook'] as $_classRef ) {
				$_procObj = & t3lib_div::getUserObj( $_classRef );
				$content = $_procObj->extraBeginGalleryProcessor( $content, $limitImages, $this );
			}
		}

		return $content;
	} # end beginGallery

	/**
	 * Just some divs needed for the gallery
	 *
	 * @return	The closed divs
	 */
	function endGallery() {
		$content = '</div>';
		if (is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraEndGalleryHook'] )) {
			foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraEndGalleryHook'] as $_classRef ) {
				$_procObj = & t3lib_div::getUserObj( $_classRef );
				$content = $_procObj->extraEndGalleryProcessor( $content, $this );
			}
		}
		return $content;
	} # end endGallery

	
	/**
	 * get the images out of a directory
	 *
	 * @param	int		$limitImages: How many images to return; default=0 list all
	 * @return	image(s)
	 */
	function getImagesDirectory($limitImages = 0) {
		if (is_dir( $this->config['startingpoint'] )) {
			$images = array();
			$images = $this->getFiles( $this->config['startingpoint'] );
			// randomise and limit image items returned from images array
			// also useful to limit items in array to 1 item for use when no javascript in browser
			// if $limitImages=0 then this if statement is bypassed and all images in images array returned for processing
			if ($limitImages > 0) {
				$images = $this->getSlicedRandomArray( $images, 0, $limitImages );
			}
			
			$content .= $this->beginGallery( $this->config['id'], $limitImages );
			
			// read the description from field in flexforms
			if ($this->config['text'] != '') {
				$caption = explode( $this->config['splitRecord'], $this->config['text'] );
			}
			
			
			
			// add the images
			foreach ( $images as $key => $value ) {
				$path = $this->config['startingpoint'] . $value;
				
				// caption text
				if ($caption[$key]) {
					$text = explode( $this->config['splitComment'], $caption[$key] );
				} else {
					// update of Xavier Perseguers (typo3@perseguers.ch) thx!
					$text = $this->readImageInfo( $this->config['startingpoint'] . $value );
				}

				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraGetImagesDirectoryHook'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraGetImagesDirectoryHook'] as $_classRef ) {
						$_procObj = & t3lib_div::getUserObj( $_classRef );
						$text = $_procObj->extraGetImagesDirectoryHook( $text, $this->config['startingpoint'] . $value, $this );
				  }
				} 
				
				// add element to slideshow
				$content.=$this->addImage(
					$path,
					$text[0], 
					$text[1],
					$this->config['showThumbs'],
					$this->config['lightbox'],
					$path,
					$limitImages
				);
			
			} # end foreach file
			

			$content .= $this->endGallery();
		} # end is_dir 
		return $content;
	} # end getImagesDirectory
	
	/**
	 * get the images out of records a user created in the backend before
	 *
	 * @param	int		$limitImages: How many images to return; default=0 list all
	 * @return	image(s)
	 */
	function getImagesRecords($limitImages = 0) {
		//prepare query
		$sort = 'sorting';
		$fields = 'title,image,description,l18n_parent';
		$tables = 'tx_rgsmoothgallery_image';
		$temp_where = 'pid IN (' . $this->config['startingpointrecords'] . ') AND hidden=0 AND deleted=0 AND sys_language_uid = ' . $GLOBALS['TSFE']->sys_language_content;
		
		$content .= $this->beginGallery( $this->config['id'], $limitImages );
		
		// add the images
		// randomise and limit image items returned from images array
		// also useful to limit items in array to 1 item for use when no javascript in browser
		// if $limitImages=0 then this if statement is bypassed and all images in images array returned for processing
		if ($limitImages > 0) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $fields, $tables, $temp_where, '', 'rand()', $limitImages );
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $fields, $tables, $temp_where, '', $sort, '' );
		}
		$this->sys_language_mode = $this->conf['sys_language_mode'] ? $this->conf['sys_language_mode'] : $GLOBALS['TSFE']->sys_language_mode;
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {
			
			if ($GLOBALS['TSFE']->sys_language_content) {
				$OLmode = ($this->sys_language_mode == 'strict' ? 'hideNonTranslated' : '');
				$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay( 'tx_rgsmoothgallery_image', $row, $GLOBALS['TSFE']->sys_language_content, $OLmode );
			}
			
			if ($row['image'] == '') {
				$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'image', $tables, 'uid=' . $row['l18n_parent'] );
				$row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res2 );
				$row['image'] = $row2['image'];
			}
			
			$path = 'uploads/tx_rgsmoothgallery/' . $row['image'];
			
			// add element to slideshow
			$content.=$this->addImage(
				$path,
				$row['title'], 
				$row['description'], 
				$this->config['showThumbs'],
				$this->config['lightbox'],
				$path,
				$limitImages
			);
			

		} # end foreach file
		

		$content .= $this->endGallery();
		return $content;
	} # getImagesRecords
	
	/**
	 * get the images out of DAM
	 *
	 * @param	int		$limitImages: How many images to return; default=0 list all
	 * @return	image(s)
	 */
	function getImagesDam($limitImages = 0) {
		// update of ian (ian@webian.it) thx!
		// check if there's a localized version of the current content object
		$uid = $this->cObj->data['uid'];
		if ($this->cObj->data['_LOCALIZED_UID']) {
			$uid = $this->cObj->data['_LOCALIZED_UID'];
		}
		$sys_language_uid = $GLOBALS['TSFE']->sys_language_content;
		
		// get all the files
		$images = tx_dam_db::getReferencedFiles( 'tt_content', $uid, 'rgsmoothgallery', 'tx_dam_mm_ref' );
		
		// randomise and limit image items returned from images array
		if ($limitImages > 0) {
			$test = ($images['files']);
			$test = $this->getSlicedRandomArray( $test, 0, $limitImages );
			$images['files'] = $test;
		}
		
		// begin gallery
		$content .= $this->beginGallery( $this->config['id'], $limitImages );
		
		// add image
		foreach ( $images['files'] as $key => $path ) {
			// get data from the single image
			$fields = 'title,description';
			$tables = 'tx_dam';
			
			// now i check the tx_dam table to see if there's a localization for the current DAM record (image)
			$temp_where = 'l18n_parent = ' . $key . ' AND sys_language_uid = ' . $sys_language_uid;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'uid', $tables, $temp_where );
			// if i find a localized record i overwrite the default language $key with the localized language $key
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res )) {
				$key = $row['uid'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result( $res );
			
			$temp_where = 'uid = ' . $key;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $fields, $tables, $temp_where );
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res );
			
			// add element to slideshow
			$content.=$this->addImage(
				$path,
				$row['title'], 
				$row['description'], 
				$this->config['showThumbs'],
				$this->config['lightbox'],
				$path,
				$limitImages
			);
		}
		
		$content .= $this->endGallery();
		return $content;
	} # end getImagesDam
	
	/**
	 * get the images out of DAM
	 *
	 * @param	int		$limitImages: How many images to return; default=0 list all
	 * @return	image(s)
	 */
	function getImagesDamCat($limitImages = 0) {
		$content .= $this->beginGallery( $this->config['id'], $limitImages );
		
		// add image
		$list = str_replace( 'tx_dam_cat_', '', $this->config['startingpointdamcat'] );
		
		$listRecursive = $this->getRecursiveDamCat( $list, $this->config['recursivedamcat'] );
		$listArray = explode( ',', $listRecursive );
		$files = Array();
		foreach ( $listArray as $cat ) {
			
			// add images from categories
			$fields = 'tx_dam.uid,tx_dam.title,tx_dam.description,tx_dam.file_name,tx_dam.file_path';
			$tables = 'tx_dam,tx_dam_mm_cat';
			$temp_where = 'tx_dam.deleted = 0 AND tx_dam.file_mime_type=\'image\' AND tx_dam.hidden=0 AND tx_dam_mm_cat.uid_foreign=' . $cat . ' AND tx_dam_mm_cat.uid_local=tx_dam.uid';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $fields, $tables, $temp_where, '', 'tx_dam.sorting' );
			
			while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {
				$files[$row['uid']] = $row; # just add the image to an array
			}
			
			$GLOBALS['TYPO3_DB']->sql_free_result( $res );
		}
		
		if ($limitImages > 0) {
			$files = $this->getSlicedRandomArray( $files, 0, $limitImages );
		}
		
		// add the image for real
		foreach ( $files as $key => $row ) {
			$path = $row['file_path'] . $row['file_name'];
			
			// add element to slideshow
			$content.=$this->addImage(
				$path,
				$row['title'], 
				nl2br($row['description']), 
				$this->config['showThumbs'],
				$this->config['lightbox'],
				$path,
				$limitImages
			);
		}
		
		$content .= $this->endGallery();
		return $content;
	}

	/**
	 * Loads all the needed javascript stuff and
	 * does the configuration of the gallery
	 *
	 * @param	boolean  $lightboxVal: Lightbox activated=
	 * @param	boolean  $thumbsVal: Thumbnail preview activated?
	 * @param	boolean  $arrowsVal: Arrows to neighbour images activated?
	 * @param	string   $durationVal: If automatic slideshow the value of the delay
	 * @param	int      $width: Width of gallery	 (depricated)
	 * @param	int      $height: Height of gallery (depricated)
	 * @param	string   $advancedSettings: Advanced configuration
	 * @param	string/int   $uniqueId: A unique ID to have more than 1 galleries on 1 page
	 * $param array    $conf: $configuration-array
	 * @return	The gallery
	 */
	function getJs($lightboxVal, $thumbsVal, $arrowsVal, $durationVal, $width, $height, $widthGallery, $heightGallery, $advancedSettings, $uniqueId, $conf, $overrideJS = '') {
		$this->conf = $conf;
		// js needed to load the gallery and to get it started
		if ($overrideJS != '') {
			$js = $overrideJS;
		} else {
			$autoplayEnable = 'false';
			if ($durationVal) {
				$autoplayEnable = 'true';
			}
			$js = '<style>

						#nbmpRs'.$this->config['id'].' {
							width: '.$widthGallery.'px;
							height:'.$heightGallery.'px;
						}
					</style>

				<script type="text/javascript">
					;(function ( $, window, document, undefined ) {
						"use strict";

						if (!$.jQueryObj) {
							$.jQueryObj = {};
						}


						var defaultSliderOptions = {
							// options go here
							// as an example, enable keyboard arrows nav
							//keyboardNavEnabled: true
							// autoHeight: true,
							// minAutoHeight: \'300px\',
							autoScaleSlider: false,
							//autoScaleSliderWidth: 200,
							//autoScaleSliderHeight: 200,
							//imageScaleMode: \'fill\',
							/*
							 imageScaleMode: function (slideObject) {
							 if (slideObject.isFullscreen) {
							 return \'fit-if-smaller\';
							 }
							 return \'fill\';
							 },
							 */
							imageScalePadding: 0,
							//slidesSpacing: 0,
							//addActiveClass: true,
							transitionType: \'fade\',
							transitionSpeed: 1000,
							//arrowsNavAutoHide: false,
							arrowsNavHideOnTouch: true,
							/*
							thumbs: {
								spacing: 1,
								navigation: false

							},
							*/
							//controlNavigation: \'thumbnails\',
							//		controlNavigation: function(slideObject) {
							//			return \'thumbnails\';
							//		},
							//controlNavigation: \'tabs\',
							//controlNavigation: \'bullets\',
							controlNavigation: \'thumbnails\',
							fullscreen: {
								enabled: true
								//buttonFS: false,
								/*
								 buttonFS: function(slideObject) {
								 return slideObject.isFullscreen;
								 },
								 */
								//					nativeFS: true,
							},
							autoPlay: {
							    enabled: ' . $autoplayEnable . ',
							    pauseOnHover: true,
							    delay: '.$durationVal.'
							},
							loop: true,
							video: {
								autoHideArrows: false,
								//    autoHideControlNav: true,
								youTubeCode: \'<iframe src="https://www.youtube.com/embed/%id%?rel=1&autoplay=1&showinfo=1" frameborder="no" width="560" height="315"></iframe>\'
							}
						};

						defaultSliderOptions.transitionType = \'slide\';


						var thumbSliderOptions = $.extend(true, {}, defaultSliderOptions);
						thumbSliderOptions.controlNavigation = \'bullets\';
					//	thumbSliderOptions.autoScaleSliderHeight = 5;

						$.jQueryObj.applySlider = function(elements) {

							$.each(elements, function (index, value) {
								if ($(value).children().length) {
									var options = defaultSliderOptions;

									if ($("*", $(value)).length > 1) {
										options = thumbSliderOptions;
										//console.log(\'thumbslideroptiones\');
									}

									var sliderObj = $(value).royalSlider(options),
										slider = sliderObj.data(\'royalSlider\');

									if (slider.hasTouch) {
										//$(".rsFullscreenBtn", sliderObj).removeClass("rsHidden");
									} else {
										var btn = $(value).find(\'.rsFullscreenBtn\').addClass(\'rsHidden\');
										$(value).hover(function () {
											btn.removeClass(\'rsHidden\');
										}, function () {
											btn.addClass(\'rsHidden\');
										});

									}

									$(\'.rsContainer\', $(value)).on(\'click\', function(e) {
										//console.log(e.target);
										if($(e.target).hasClass(\'rsSlide\')) {
											slider.exitFullscreen();
										}
									});


									if (sliderObj) {


										slider.updateSliderSize = function (force) {
											$.rsProto.updateSliderSize.call(this, force);
										};

										slider.ev.on(\'rsEnterFullscreen\', function () {
					//                        slider.stopAutoPlay();
											//    slider.st.imageScaleMode = "fit-if-smaller";
											slider.st.fullscreen.buttonFS = true;
											setTimeout(function () {
												//        slider.updateSliderSize(true);
											}, 200);
										});

										slider.ev.on(\'rsExitFullscreen\', function () {
					//                        slider.startAutoPlay();
											slider.stopVideo();
											//    slider.st.imageScaleMode = "fill";
					//                        slider.st.fullscreen.buttonFS = false;
											setTimeout(function () {
												//        slider.updateSliderSize(true);
											}, 2000);
										});

										slider.ev.on(\'rsOnCreateVideoElement\', function (e, url) {
											if (!slider.isFullscreen) {
												slider.enterFullscreen();
												setTimeout(function () {
													slider.updateSliderSize(true);
												}, 200);
											}
											setTimeout(function () {
												slider.updateSliderSize(true);
											}, 20);
											// url - path to video from data-rsVideo attribute
											// slider.videoObj - jQuery object that holds video HTML code
											// slider.videoObj must be IFRAME, VIDEO or EMBED element, or have class rsVideoObj
											//					slider.videoObj = $(\'<iframe title="YouTube video player" width="640" height="480" src="\' + url + \'?rel=0&amp;hd=1?autoplay=1&amp;controls=1&amp;showinfo=1&amp;rel=0&amp;autohide=0&rel=0" frameborder="0" allowfullscreen></iframe>\');
										});
										slider.ev.on(\'rsOnDestroyVideoElement\', function (e) {
					//                        slider.stopAutoPlay();
											// slider.videoObj - jQuery object that holds video HTML code
											if (slider.numSlides == 1) {
												slider.exitFullscreen();
											}
										});

										$(value).closest(".royalSlider").mouseleave(function (e) {
											//slider.goTo(0);
					//                        slider.toggleAutoPlay();
										});

										slider.ev.on(\'rsSlideClick\', function () {
											//console.log(\'slide click\', slider, slider.ev.target);
											if (slider.isFullscreen) {
												//slider.exitFullscreen();
											}
										});
									}
								}

							});
						}

					})( jQuery, window, document );

					jQuery(document).ready(function ($) {

						$.jQueryObj.applySlider($(\'.royalSlider\'));

					});
    			</script>';
			if ($this->conf['noscript'] == 1) {
				$js .= '<noscript>
    		' . $this->getImageDifferentPlaces(1) . '
    		</noscript>';
			}
		}
		return $js;
	}
	
	/**
	 * depending on the chosen settings the images come from different places 
	 *
	 * @param	string  $limitImages: How many images to return; default=0 list all
	 * @return	The image(s)
	 */
	function getImageDifferentPlaces($limitImages = 0) {
		if ($this->config['mode'] == 'DIRECTORY') {
			$content .= $this->getImagesDirectory( $limitImages );
		} elseif ($this->config['mode'] == 'RECORDS') {
			$content .= $this->getImagesRecords( $limitImages );
		} elseif ($this->config['mode'] == 'DAM') {
			$content .= $this->getImagesDam( $limitImages );
		} elseif ($this->config['mode'] == 'DAMCAT') {
			$content .= $this->getImagesDamCat( $limitImages );
		}
		
		// hook
		if (is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraDifferentPlaces'] )) {
			foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraDifferentPlaces'] as $_classRef ) {
				$_procObj = & t3lib_div::getUserObj( $_classRef );
				$content = $_procObj->extraBeginGalleryProcessor( $content, $limitImages, $this );
			}
		}
		return $content;
	}

	/**
	 * Adds a single image to the gallery
	 *
	 * @param	string  $path: Path to the image
	 * @param	string  $title: Title for the image
	 * @param	string  $description: Description for the image
	 * @param	string  $thumb: Url to the thumbnail image
	 * @param	string  $lightbox: Url to the lightbox image
	 * @param	string  $uniqueID: Unique-ID to identify an image (uid or path)
	 * @param	string  $limitImages: How many images to return; default=0 list all
	 * @return	The single image
	 */
	function addImage($path, $title, $description, $thumb, $lightbox, $uniqueID, $limitImages = 0) {
		// count of images
		if ($limitImages > 1 || $limitImages == 0) {
			$this->config['count'] ++;
		}

		//  generate images
		if ($this->config['watermarks']) {
			$imgTSConfigBig = $this->conf['big2.'];
			$imgTSConfigBig['file.']['10.']['file'] = $path;
			$imgTSConfigLightbox = $this->conf['lightbox2.'];
			$imgTSConfigLightbox['file.']['10.']['file'] = $path;
		} else {
			$imgTSConfigBig = $this->conf['big.'];
			$imgTSConfigBig['file'] = $path;
			$imgTSConfigLightbox = $this->conf['lightbox.'];
			$imgTSConfigLightbox['file'] = $path;
		}
		$bigImage = $this->cObj->IMG_RESOURCE( $imgTSConfigBig );

		$lightbox = /*($lightbox == '#' || $lightbox == '' || $this->config['lightbox'] != 1) ? 'javascript:void(0)' : */ $this->cObj->IMG_RESOURCE( $imgTSConfigLightbox );
		$lightbox = str_replace( ' ', '%20', $lightbox ); // search for empty chars, thx maxhb
		$lightBoxImage = '<a href="' . $lightbox . '" title="' . $this->pi_getLL( 'textOpenImage' ) . '" class="open"></a>';

		if ($thumb) {
			$imgTSConfigThumb = $this->conf['thumb.'];
			$imgTSConfigThumb['file'] = $path;
#			$thumbImage = '<img src="' . $this->cObj->IMG_RESOURCE( $imgTSConfigThumb ) . '" class="thumbnail" />';
			$thumbImage = $this->cObj->IMG_RESOURCE( $imgTSConfigThumb );
		}

		// just add the wraps if there is a text for it or if there is no lightbox which needs the title of course!
		if ($this->config['hideInfoPane'] != 1 || $lightbox != 'javascript:void(0)') {
			$text = (! $title) ? '' : "<h3>$title</h3>";
			$text .= (! $description) ? '' : "<p>$description</p>";
		}

		// if just 1 image should be returned
		if ($limitImages == 1) {
			return '<img src="' . $bigImage . '" class="full" />';
		}

		// build the image element
		$singleImage = '<img
			class="rsImg"
     		src="' . $bigImage . '"
			data-rsTmb="' . $thumbImage . '"
			data-rsBigImg="' . $lightbox . '"
			/>';

		// Adds hook for processing the image
		$config['path'] = $path;
		$config['title'] = $title;
		$config['description'] = $description;
		$config['uniqueID'] = $uniqueID;
		$config['thumb'] = $thumb;
		$config['lightbox'] = $lightbox;
		$config['limitImages'] = $limitImages;
		$config['lightBoxCode'] = $lightBoxImage;
		if (is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraImageHook'] )) {
			foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraImageHook'] as $_classRef ) {
				$_procObj = & t3lib_div::getUserObj( $_classRef );
				$singleImage = $_procObj->extraImageProcessor( $singleImage, $config, $this );
			}
		}

		return $singleImage;
	}
	
	/**
	 * Gets all image files out of a directory 
	 *
	 * @param	string  $path: Path to the directory
	 * @return Array with the images
	 */
	function getFiles($path, $extra = "") {
		$files = Array();
		
		// check for needed slash at the end
		$length = strlen( $path );
		if ($path {$length - 1} != '/') {
			$path .= '/';
		}
		
		$imagetypes = $this->conf["filetypes"] ? explode(',', $this->conf["filetypes"]) : array(
			'jpg',
			'jpeg',
			'gif',
			'png'
		);
		
		if ($dir = dir( $path )) {	
			while ( false !== ($file = $dir->read()) ) {
				if ($file != '.' && $file != '..') {
					$ext = strtolower( substr( $file, strrpos( $file, '.' ) + 1 ) );
					if (in_array( $ext, $imagetypes )) {
						array_push( $files, $extra . $file );
					}
					else if ($this->conf["recursive"] == '1' && is_dir( $path . "/" . $file )) {
						$dirfiles = $this->getFiles( $path . "/" . $file, $extra . $file . "/" );
						if (is_array( $dirfiles )) {
							$files = array_merge( $files, $dirfiles );
						}
					}
				}
			}
			
			$dir->close();
			// sort files, thx to all
			sort( $files );
			
			return $files;
		}
	} # end getFiles
	

	/**
	 * Gets the path to a file, needed to translate the 'EXT:extkey' into the real path
	 *
	 * @param	string  $path: Path to the file
	 * @return the real path
	 */
	function getPath($path) {
		if (substr( $path, 0, 4 ) == 'EXT:') {
			$keyEndPos = strpos( $path, '/', 6 );
			$key = substr( $path, 4, $keyEndPos - 4 );
			$keyPath = t3lib_extMgm::siteRelpath( $key );
			$newPath = $keyPath . substr( $path, $keyEndPos + 1 );
			return $newPath;
		} else {
			return $path;
		}
	} # end getPath
	

	/**
	 * Random view of an array and slice it afterwards, preserving the keys
	 *
	 * @param	array  $array: Array to modify
	 * @param	array  $offset: Where to start the slicing
	 * @param	array  $length: Length of the sliced array
	 * @return the randomized and sliced array
	 */
	function getSlicedRandomArray($array, $offset, $length) {
		// shuffle
		$new_arr = array();
		while ( count( $array ) > 0 ) {
			$val = array_rand( $array );
			$new_arr[$val] = $array[$val];
			unset( $array[$val] );
		}
		$result = $new_arr;
		
		// slice
		$result2 = array();
		$i = 0;
		if ($offset < 0)
			$offset = count( $result ) + $offset;
		if ($length > 0) {
			$endOffset = $offset + $length;
		}
		else if ($length < 0) {
			$endOffset = count( $result ) + $length;
		}
		else {
			$endOffset = count( $result );
		}
			
		// collect elements
		foreach ( $result as $key => $value ) {
			if ($i >= $offset && $i < $endOffset)
				$result2[$key] = $value;
			$i ++;
		}
		return $result2;
	}
	
	/**
	 * get a list of recursive categories
	 *
	 * @param	string		$id: comma seperated list of ids
	 * @param	int		$level: the level for recursion 	 
	 * @return	image(s)
	 */
	function getRecursiveDamCat($id, $level = 0) {
		$result = $id . ','; # add id of 1st level 
		$idList = explode( ',', $id );
		
		if ($level > 0) {
			$level --;
			
			foreach ( $idList as $key => $value ) {
				$where = 'hidden=0 AND deleted=0 AND parent_id=' . $id;
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'uid', 'tx_dam_cat', $where );
				while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {
					$rec = $this->getRecursiveDamCat( $row['uid'], $level );
					if ($rec != '') {
						$result .= $rec . ',';
					}
				}
			} # end for each
		} # end if level
		

		$result = str_replace( ',,', ',', $result );
		$result = substr( $result, 0, - 1 );
		return $result;
	}
	
	/**
	 * Get the value out of the flexforms and if empty, take if from TS
	 *
	 * @param	string		$sheet: The sheed of the flexforms
	 * @param	string		$key: the name of the flexform field
	 * @param	string		$confOverride: The value of TS for an override
	 * @return	string	The value of the locallang.xml
	 */
	function getFlexform($sheet, $key, $confOverride = '') {
		// Default sheet is sDEF
		$sheet = ($sheet == '') ? $sheet = 'sDEF' : $sheet;
		$flexform = $this->pi_getFFvalue( $this->cObj->data['pi_flexform'], $key, $sheet );
		
		// possible override through TS
		if ($confOverride == '') {
			return $flexform;
		} else {
			$value = $flexform ? $flexform : $this->conf[$confOverride];
			return $value;
		}
	}
	
	/**
	 * Read data from an image or an associated text file.
	 *
	 * @param	string		$image: path to the image
	 * @return	array		title, description and author
	 */
	function readImageInfo($image) {
		$exifData = $this->readExif( $image );
		$iptcData = $this->readIptc( $image );
		$textData = $this->readTextComment( $image );
		
		$this->getPrioritizedContent( $exifData, 'author', 'description' );
		
		// Text data has precedence, then EXIF, then IPTC
		$title = $this->getPrioritizedContent( array($textData['title'], $exifData['title'], $iptcData['title'] ) );
		$description = $this->getPrioritizedContent( array($textData['description'], $exifData['description'], $iptcData['description'] ) );
		$author = $this->getPrioritizedContent( array($textData['author'], $exifData['author'], $iptcData['author'] ) );
		
		// Append author to the description
		if ($author && $this->conf['author']) {
			if ($description)
				$description .= ' ';
			$description .= str_replace( '|', $author, $this->pi_getLL('authorWrap') );
		}
		
		return array($title, $description );
	}
	
	/**
	 * Read EXIF data from an image.
	 *
	 * @param	string		$image: path to the image
	 * @return	array		title, description and author
	 */
	function readExif($image) {
		if (! t3lib_div::isAbsPath( $image )) {
			$image = t3lib_div::getFileAbsFileName( $image );
		}
		
		$data = array('title' => '', 'description' => '', 'author' => '' );
		
		if (! t3lib_div::inArray( get_loaded_extensions(), 'exif' ) || $this->conf['exif']!=1) { // If there is no EXIF Support at your installation
			return $data;
		}
		
		if (file_exists( $image ))
			$image_info = getimagesize( $image );
		if ($image_info[2] == 2) { // check for correct image-type
			//ini_set('exif.encode_unicode', 'UTF-8'); //Set encoding to Unicode, if not set in php.ini
			$exif_array = exif_read_data( $image, TRUE, FALSE ); // Load all EXIF informations from the original Pic in an Array
			$exif_array['Comments'] = htmlentities( str_replace( "\n", '<br />', $exif_array['Comments'] ) ); // Linebreak
			

			$data['title'] = $this->getPrioritizedContent( $exif_array, 'Title', 'Subject' );
			$data['description'] = $this->getPrioritizedContent( $exif_array, 'Comments', 'ImageDescription' );
			$data['author'] = $this->getPrioritizedContent( $exif_array, 'Author', 'Artist' );
		}
		
		return $data;
	}
	
	/**
	 * Read IPTC data from an image.
	 *
	 * @param	string		$image: path to the image
	 * @return	array		title, description and author
	 */
	function readIptc($image) {
		if (! t3lib_div::isAbsPath( $image )) {
			$image = t3lib_div::getFileAbsFileName( $image );
		}
		
		$data = array('title' => '', 'description' => '', 'author' => '' );

		if ($this->conf['iptc']!=1) { // If there is no EXIF Support at your installation
			return $data;
		}

		
		$info = NULL;
		getimagesize( $image, $info );
		if (is_array( $info )) {
			$iptc = iptcparse( $info["APP13"] );
			
			$data['title'] = $this->getPrioritizedContent( $iptc, '2#005', '2#105' ); // Title then Headline
			// Array is returned, use first item
			$data['title'] = $data['title'][0];
			
			$data['description'] = $iptc['2#120'][0];
			$data['author'] = $iptc['2#080'][0];
		}
		
		return $data;
	}
	
	/**
	 * Read image information from a associated text file.
	 * The text file should have the same name as the image but
	 * should end with '.txt'. Format
	 * <pre>
	 * {{title}}
	 * {{description}}
	 * {{author}}
	 * </pre>
	 *
	 * @param	string		$image: path to the image
	 * @return	array		title, description and author
	 */
	function readTextComment($image) {
		if (! t3lib_div::isAbsPath( $image )) {
			$image = t3lib_div::getFileAbsFileName( $image );
		}
		
		$data = array('title' => '', 'description' => '', 'author' => '' );
		
		$textfile = substr( $image, 0, strrpos( $image, '.' ) ) . '.txt';
		
		if (file_exists( $textfile )) {
			$lines = file( $textfile );
			
			if (count( $lines )) {
				$data['title'] = $lines[0];
				$data['description'] = $lines[1];
				$data['author'] = $lines[2];
			}
		}
		
		return $data;
	}

	/**
	 * Extract content from an array. First key that is
	 * associated to a content that is not empty will be
	 * returned. If keys are null then it returns the first
	 * non-empty element.
	 *
	 * @param	array		$array: array whose content should be extracted
	 * @param	mixed		key1
	 * @param	mixed		key2
	 * @param	mixed		...
	 * @return	string
	 */
	function getPrioritizedContent($array) {
		$keys = func_get_args();
		array_shift( $keys );
		
		if (! count( $keys ))
			$keys = array_keys( $array );
		
		foreach ( $keys as $key ) {
			if ($array[$key])
				return $array[$key];
		}
		
		return '';
	}
	
	    
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgsmoothgallery/pi1/class.tx_rgsmoothgallery_pi1.php'])	{
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgsmoothgallery/pi1/class.tx_rgsmoothgallery_pi1.php']);
}
?>
