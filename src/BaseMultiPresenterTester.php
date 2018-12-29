<?php

namespace Helbrary\NetteTesterExtension;


class BaseMultiPresenterTester extends PresenterTester
{

    public function __construct($bootstrapPath = __DIR__ . '/../../../../app/bootstrap.php')
    {
        parent::__construct($bootstrapPath);
    }


    /**
     * @param array $actions
     */
    public function checkWihtoutErorrs(array $actions) {
        foreach ($actions as $presenterName => $actionsData) {
            foreach ($actionsData['actions'] as $action => $actionData) {
                $parameters = isset($actionsData['parameters']) ? $actionsData['parameters'] : [];
                $parameters['action'] = $action;

                $this->setPresenterName($presenterName);
                $this->checkRequestNoError(
                    $parameters,
                    isset($actionData['method']) ? $actionData['method'] : 'GET',
                    isset($actionData['userId']) ? $actionData['userId'] : NULL,
                    isset($actionData['userRole']) ? $actionData['userRole'] : NULL,
                    isset($actionData['identityData']) ? $actionData['identityData'] : NULL
                );
            }
        }
    }



}