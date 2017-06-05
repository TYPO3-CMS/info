<?php
namespace TYPO3\CMS\Info\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Module\BaseScriptClass;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Script Class for the Web > Info module
 * This class creates the framework to which other extensions can connect their sub-modules
 */
class InfoModuleController extends BaseScriptClass
{
    /**
     * @var array
     */
    public $pageinfo;

    /**
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected $backendUser;

    /**
     * @var \TYPO3\CMS\Lang\LanguageService
     */
    protected $languageService;

    /**
     * The name of the module
     *
     * @var string
     */
    protected $moduleName = 'web_info';

    /**
     * ModuleTemplate Container
     *
     * @var ModuleTemplate
     */
    protected $moduleTemplate;

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
        $this->languageService = $GLOBALS['LANG'];
        $this->languageService->includeLLFile('EXT:lang/Resources/Private/Language/locallang_mod_web_info.xlf');

        $this->backendUser = $GLOBALS['BE_USER'];

        $this->MCONF = [
            'name' => $this->moduleName,
        ];
    }

    /**
     * Initialize module header etc and call extObjContent function
     */
    public function main()
    {
        // We leave this here because of dependencies to submodules
        $this->doc = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Template\DocumentTemplate::class);

        // The page will show only if there is a valid page and if this page
        // may be viewed by the user
        $this->pageinfo = BackendUtility::readPageAccess($this->id, $this->perms_clause);
        if ($this->pageinfo) {
            $this->moduleTemplate->getDocHeaderComponent()->setMetaInformation($this->pageinfo);
        }
        $access = is_array($this->pageinfo);
        if ($this->id && $access || $this->backendUser->user['admin'] && !$this->id) {
            if ($this->backendUser->user['admin'] && !$this->id) {
                $this->pageinfo = ['title' => '[root-level]', 'uid' => 0, 'pid' => 0];
            }
            // JavaScript
            $this->moduleTemplate->addJavaScriptCode(
                'WebFuncInLineJS',
                'if (top.fsMod) top.fsMod.recentIds["web"] = ' . (int)$this->id . ';
				function jumpToUrl(URL) {
					window.location.href = URL;
					return false;
				}
				'
            );
            // Setting up the context sensitive menu:
            $this->moduleTemplate->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/ContextMenu');

            $this->view = $this->getFluidTemplateObject();
            $this->view->assign('moduleName', BackendUtility::getModuleUrl($this->moduleName));
            $this->view->assign('versionSelector', $this->moduleTemplate->getVersionSelector($this->id, 1));
            $this->view->assign('functionMenuModuleContent', $this->getExtObjContent());
            // Setting up the buttons and markers for docheader
            $this->getButtons();
            $this->generateMenu();
            $this->content .= $this->view->render();
        } else {
            // If no access or if ID == zero
            $this->content = $this->moduleTemplate->header($this->languageService->getLL('title'));
        }
    }

    /**
     * Injects the request object for the current request or subrequest
     * Then checks for module functions that have hooked in, and renders menu etc.
     *
     * @param ServerRequestInterface $request the current request
     * @param ResponseInterface $response
     * @return ResponseInterface the response with the content
     */
    public function mainAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $GLOBALS['SOBE'] = $this;
        $this->init();

        // Checking for first level external objects
        $this->checkExtObj();

        // Checking second level external objects
        $this->checkSubExtObj();
        $this->main();

        $this->moduleTemplate->setContent($this->content);
        $response->getBody()->write($this->moduleTemplate->renderContent());
        return $response;
    }

    /**
     * Create the panel of buttons for submitting the form or otherwise perform operations.
     */
    protected function getButtons()
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        // View page
        $viewButton = $buttonBar->makeLinkButton()
            ->setHref('#')
            ->setOnClick(BackendUtility::viewOnClick(
                $this->pageinfo['uid'],
                '',
                BackendUtility::BEgetRootLine($this->pageinfo['uid'])
            ))
            ->setTitle($this->languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.showPage'))
            ->setIcon($this->moduleTemplate->getIconFactory()->getIcon('actions-document-view', Icon::SIZE_SMALL));
        $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
        // Shortcut
        $shortCutButton = $buttonBar->makeShortcutButton()
            ->setModuleName($this->moduleName)
            ->setDisplayName($this->MOD_MENU['function'][$this->MOD_SETTINGS['function']])
            ->setGetVariables([
                'M',
                'id',
                'edit_record',
                'pointer',
                'new_unique_uid',
                'search_field',
                'search_levels',
                'showLimit'
            ])
            ->setSetVariables(array_keys($this->MOD_MENU));
        $buttonBar->addButton($shortCutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        // CSH
        $cshButton = $buttonBar->makeHelpButton()
          ->setModuleName('xMOD_csh_corebe')
          ->setFieldName('pagetree_overview');
        $buttonBar->addButton($cshButton);
    }

    /**
     * Generate the ModuleMenu
     */
    protected function generateMenu()
    {
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('WebInfoJumpMenu');
        foreach ($this->MOD_MENU['function'] as $controller => $title) {
            $item = $menu
                ->makeMenuItem()
                ->setHref(
                    BackendUtility::getModuleUrl(
                        $this->moduleName,
                        [
                            'id' => $this->id,
                            'SET' => [
                                'function' => $controller
                            ]
                        ]
                    )
                )
                ->setTitle($title);
            if ($controller === $this->MOD_SETTINGS['function']) {
                $item->setActive(true);
            }
            $menu->addMenuItem($item);
        }
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * Returns the ModuleTemplate container
     * This is used by PageLayoutView.php
     *
     * @return ModuleTemplate
     */
    public function getModuleTemplate()
    {
        return $this->moduleTemplate;
    }

    /**
     * returns a new standalone view, shorthand function
     *
     * @return StandaloneView
     */
    protected function getFluidTemplateObject()
    {
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths([GeneralUtility::getFileAbsFileName('EXT:info/Resources/Private/Layouts')]);
        $view->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:info/Resources/Private/Partials')]);
        $view->setTemplateRootPaths([GeneralUtility::getFileAbsFileName('EXT:info/Resources/Private/Templates')]);

        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:info/Resources/Private/Templates/Main.html'));

        $view->getRequest()->setControllerExtensionName('info');
        return $view;
    }
}
