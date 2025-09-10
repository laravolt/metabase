<?php

namespace Laravolt\Metabase;

use Illuminate\View\Component;

class MetabaseComponent extends Component
{
    public ?int $dashboard;

    public ?int $question;

    public bool $bordered;

    public bool $titled;

    public ?string $theme;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $params;

    /**
     * Create a new component instance.
     *
     * @param int|null $dashboard
     * @param int|null $question
     * @param array<string, mixed> $params
     * @param bool $bordered
     * @param bool $titled
     * @param string|null $theme
     */
    public function __construct(?int $dashboard = null, ?int $question = null, array $params = [], bool $bordered = false, bool $titled = false, ?string $theme = null)
    {
        $this->dashboard = $dashboard;
        $this->question = $question;
        $this->params = $params;
        $this->bordered = $bordered;
        $this->titled = $titled;
        $this->theme = $theme;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        /** @var MetabaseService $metabase */
        $metabase = app(MetabaseService::class);
        $metabase->setParams($this->params ?? []);
        $metabase->setAdditionalParams($this->getAdditionalParams());
        $iframeUrl = $metabase->generateEmbedUrl($this->dashboard, $this->question);
        
        /** @var \Illuminate\Contracts\View\View $view */
        $view = view('metabase::iframe', compact('iframeUrl'));
        return $view;
    }


    /**
     * Get additional parameters for the iframe URL.
     *
     * @return array<string, mixed>
     */
    private function getAdditionalParams(): array
    {
        $additionalParameters = [
            'bordered' => $this->bordered,
            'titled' => $this->titled,
        ];

        if ($this->theme) {
            $additionalParameters['theme'] = $this->theme;
        }

        return $additionalParameters;
    }
}
