<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

class BaseController extends Controller
{


    /***
     * Return empty parameters
     * @param $arrParams
     * @param Request $request
     * @return array
     */
    protected function validateParams($arrParams, $form)
    {
        $missingParameters = array_filter(array_map(function ($param) use ($form) {
            return (!isset($form[$param]) || empty($form[$param])) ? $param : '';
        }, $arrParams));

        if (!empty($missingParameters))
            throw new Exception(" The following parameters are missing " . json_encode(array_values($missingParameters)));

    }


    /***
     * Creates form builder to manage the form data
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getFormBuilder(array $columns)
    {
        $form = $this->createFormBuilder();
        foreach ($columns as $param)
            $form = $form->add($param);
        return $form;
    }
}