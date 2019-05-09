<?php

class imageApplication extends controllerApplication
{
    protected $applicationName = 'image';
    protected $width;
    protected $height;
    protected $id;
    protected $fileName;
    protected $layoutType;
    protected $angle;
    protected $save;
    public $rendererName = 'imageProcess';

    public function initialize()
    {
        $this->createRenderer();
    }

    public function execute($controller)
    {
        $this->processRequestParameters();
        $imagePreset = $this->getService('ConfigManager')->get("images-desktop.{$this->layoutType}");
        if (isset($imagePreset['path'])) {
            $originalFilePath = $this->getService('PathsManager')->getPath($imagePreset['path']) . $this->id;
        } else {
            $originalFilePath = $this->getService('PathsManager')->getPath('uploads') . $this->id;
        }
        $result = false;
        if (is_file($originalFilePath)) {
            $this->renderer->assign('registerImage', [
                'source',
                $originalFilePath,
            ]);
            if (!empty($imagePreset['images'])) {
                foreach ($imagePreset['images'] as &$imageInfo) {
                    $this->renderer->assign('registerImage', $imageInfo);
                }
            }
            if (!empty($imagePreset['filters'])) {
                foreach ($imagePreset['filters'] as &$filter) {
                    $this->renderer->assign('registerFilter', $filter);
                }
            }

            if (!empty($imagePreset['format'])) {
                if ($contentTypes = $this->getService('requestHeadersManager')->getAcceptedTypes()) {
                   if (isset ($contentTypes['image/webp'])) {
                        $imagePreset['format'][1] = 'webp';
                   }
                }
                $this->renderer->assign('registerExport', $imagePreset['format']);
            } else {
                $this->renderer->assign('registerExport', null);
            }
            $this->renderer->setContentDisposition('inline');
            $this->renderer->display();
            $result = true;
        }

        if (!$result) {
            $this->renderer->fileNotFound();
        }
    }

    public function processRequestParameters()
    {
        $controller = controller::getInstance();
        if ($controller->getParameter('id')) {
            $this->id = $controller->getParameter('id');
        }
        if ($controller->getParameter('width')) {
            $this->width = $controller->getParameter('width');
        }
        if ($controller->getParameter('height')) {
            $this->height = $controller->getParameter('height');
        }
        if ($controller->getParameter('angle')) {
            $this->angle = $controller->getParameter('angle');
        }
        if ($controller->getParameter('filename')) {
            $this->fileName = $controller->getParameter('filename');
        }
        if ($controller->getParameter('type')) {
            $this->layoutType = $controller->getParameter('type');
        }
        $this->save = !!$controller->getParameter('save');
    }

    public function deprecatedParametersRedirection()
    {
        return true;
    }
}
