<?php

namespace Appsas;

class Response
{
    public mixed $content;
    public bool $redirect = false;
    public ?string $redirectUrl;

    public function __construct(mixed $content)
    {
        $this->content = $content;
    }

    public function redirect(string $url, mixed $content = null): self
    {
        $this->content = $content;
        $this->redirect = true;
        $this->redirectUrl = $url;
        return $this;
    }

    public function send(): void
    {
        /** @var Output $output */
        $output = App::resolve(Output::class);

        // Iškviečiamas Render klasės objektas ir jo metodas setContent()
        $render = new HtmlRender($output);
        $render->setContent($this->content);

        // Spausdinam viska kas buvo 'Storinta' Output klaseje
        $output->print();

    }
}