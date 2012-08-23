<?php
namespace TYPO3\CMS\Info\Controller;

/**
 * Script Class for the Web > Info module
 * This class creates the framework to which other extensions can connect their sub-modules
 *
 * @author Kasper Skårhøj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class InfoModuleController extends \TYPO3\CMS\Backend\Module\BaseScriptClass {

	// Internal, dynamic:
	/**
	 * @todo Define visibility
	 */
	public $be_user_Array;

	/**
	 * @todo Define visibility
	 */
	public $CALC_PERMS;

	/**
	 * @todo Define visibility
	 */
	public $pageinfo;

	/**
	 * Document Template Object
	 *
	 * @var \TYPO3\CMS\Backend\Template\MediumDocumentTemplate
	 * @todo Define visibility
	 */
	public $doc;

	/**
	 * Initialize module header etc and call extObjContent function
	 *
	 * @return void
	 * @todo Define visibility
	 */
	public function main() {
		// Access check...
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if ($this->id && $access || $GLOBALS['BE_USER']->user['admin'] && !$this->id) {
			$this->CALC_PERMS = $GLOBALS['BE_USER']->calcPerms($this->pageinfo);
			if ($GLOBALS['BE_USER']->user['admin'] && !$this->id) {
				$this->pageinfo = array('title' => '[root-level]', 'uid' => 0, 'pid' => 0);
			}
			$this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Template\\DocumentTemplate');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];
			$this->doc->setModuleTemplate('templates/info.html');
			$this->doc->tableLayout = array(
				'0' => array(
					'0' => array('<td valign="top"><strong>', '</strong></td>'),
					'defCol' => array(('<td><img src="' . $this->doc->backPath) . 'clear.gif" width="10" height="1" alt="" /></td><td valign="top"><strong>', '</strong></td>')
				),
				'defRow' => array(
					'0' => array('<td valign="top">', '</td>'),
					'defCol' => array(('<td><img src="' . $this->doc->backPath) . 'clear.gif" width="10" height="1" alt="" /></td><td valign="top">', '</td>')
				)
			);
			// JavaScript
			$this->doc->JScode = $this->doc->wrapScriptTags('
				script_ended = 0;
				function jumpToUrl(URL) {	//
					window.location.href = URL;
				}
			');
			$this->doc->postCode = $this->doc->wrapScriptTags(('
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = ' . intval($this->id)) . ';
			');
			// Setting up the context sensitive menu:
			$this->doc->getContextMenuCode();
			$this->doc->form = '<form action="index.php" method="post" name="webinfoForm">';
			$vContent = $this->doc->getVersionSelector($this->id, 1);
			if ($vContent) {
				$this->content .= $this->doc->section('', $vContent);
			}
			$this->extObjContent();
			// Setting up the buttons and markers for docheader
			$docHeaderButtons = $this->getButtons();
			$markers = array(
				'CSH' => $docHeaderButtons['csh'],
				'FUNC_MENU' => \TYPO3\CMS\Backend\Utility\BackendUtility::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']),
				'CONTENT' => $this->content
			);
			// Build the <body> for the module
			$this->content = $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
		} else {
			// If no access or if ID == zero
			$this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Template\\MediumDocumentTemplate');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];
			$this->content = $this->doc->header($GLOBALS['LANG']->getLL('title'));
			$this->content .= $this->doc->spacer(5);
			$this->content .= $this->doc->spacer(10);
		}
		// Renders the module page
		$this->content = $this->doc->render($GLOBALS['LANG']->getLL('title'), $this->content);
	}

	/**
	 * Print module content (from $this->content)
	 *
	 * @return void
	 * @todo Define visibility
	 */
	public function printContent() {
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

	/**
	 * Create the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return array All available buttons as an assoc. array
	 */
	protected function getButtons() {
		$buttons = array(
			'csh' => '',
			'view' => '',
			'shortcut' => ''
		);
		// CSH
		$buttons['csh'] = \TYPO3\CMS\Backend\Utility\BackendUtility::cshItem('_MOD_web_info', '', $GLOBALS['BACK_PATH'], '', TRUE);
		// View page
		$buttons['view'] = ((((('<a href="#" onclick="' . htmlspecialchars(\TYPO3\CMS\Backend\Utility\BackendUtility::viewOnClick($this->pageinfo['uid'], $GLOBALS['BACK_PATH'], \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine($this->pageinfo['uid'])))) . '" title="') . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.showPage', 1)) . '">') . \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('actions-document-view')) . '</a>';
		// Shortcut
		if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$buttons['shortcut'] = $this->doc->makeShortcutIcon('id, edit_record, pointer, new_unique_uid, search_field, search_levels, showLimit', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']);
		}
		return $buttons;
	}

}


?>