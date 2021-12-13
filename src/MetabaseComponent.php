<?php

namespace Laravolt\Metabase;

use Illuminate\View\Component;

class MetabaseComponent extends Component
{
    public ?string $dashboard;

    public ?string $question;

    /**
     * @var string[]
     */
    public ?array $params;

    /**
     * Create a new component instance.
     *
     * @param string|null $dashboard
     * @param string|null $question
     * @param array<string> $params
     */
    public function __construct(?string $dashboard = null, ?string $question = null, array $params = [])
    {
        $this->dashboard = $dashboard;
        $this->question = $question;
        $this->params = $params;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $metabase = app(MetabaseService::class);
        $metabase->setParams($this->params);
        $iframeUrl = $metabase->generateEmbedUrl((int) $this->dashboard, (int) $this->question);
        return view('metabase::iframe', compact('iframeUrl'));
    }
}
