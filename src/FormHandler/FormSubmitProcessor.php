<?php
namespace Hostnet\Component\FormHandler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Processor when handling the submit of a form.
 *
 * @internal
 */
final class FormSubmitProcessor
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var callable|null
     */
    private $on_success;

    /**
     * @var callable|null
     */
    private $on_failure;

    /**
     * @param FormInterface $form
     * @param callable|null $on_success
     * @param callable|null $on_failure
     */
    public function __construct(
        FormInterface $form,
        callable $on_success = null,
        callable $on_failure = null
    ) {
        $this->on_success = $on_success;
        $this->on_failure = $on_failure;
        $this->form       = $form;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function process(Request $request)
    {
        $this->form->handleRequest($request);

        if (!$this->form->isSubmitted()) {
            return null;
        }

        if ($this->form->isValid()) {
            if (is_callable($this->on_success)) {
                return call_user_func($this->on_success, $this->form->getData(), $this->form, $request);
            }
        } elseif (is_callable($this->on_failure)) {
            return call_user_func($this->on_failure, $this->form->getData(), $this->form, $request);
        }
    }
}
