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
            foreach ($actionsData['actions'] as $actionDataVariant) {
                $this->setPresenterName($presenterName);
                $this->checkRequestNoError(
                    $actionDataVariant['parameters'],
                    isset($actionDataVariant['method']) ? $actionDataVariant['method'] : 'GET',
                    isset($actionDataVariant['userId']) ? $actionDataVariant['userId'] : NULL,
                    isset($actionDataVariant['userRole']) ? $actionDataVariant['userRole'] : NULL,
                    isset($actionDataVariant['identityData']) ? $actionDataVariant['identityData'] : NULL
                );
            }
        }
    }


    /**
     * @param array $actions
     * @param string $redirectTo
     * @param bool $ignoreRedirectParameters
     */
    public function checkRedirectsTo(array $actions, string $redirectTo, bool $ignoreRedirectParameters = TRUE) {
        foreach ($actions as $presenterName => $actionsData) {
            foreach ($actionsData['actions'] as $actionDataVariant) {
                $this->setPresenterName($presenterName);
                $this->checkRedirectTo(
                    $actionDataVariant['parameters'],
                    $redirectTo,
                    isset($actionDataVariant['method']) ? $actionDataVariant['method'] : 'GET',
                    isset($actionDataVariant['userId']) ? $actionDataVariant['userId'] : NULL,
                    isset($actionDataVariant['userRole']) ? $actionDataVariant['userRole'] : NULL,
                    $ignoreRedirectParameters,
                    isset($actionDataVariant['identityData']) ? $actionDataVariant['identityData'] : NULL
                );
            }
        }
    }


}
