<?php

namespace Laravolt\Metabase\Controllers;

use Illuminate\Routing\Controller;

class EmbedController extends Controller
{
    /**
     * @param int $id
     * @param array<string> $params
     * @return \Illuminate\Contracts\View\View
     */
    public function show(int $id, array $params = []): \Illuminate\Contracts\View\View
    {
        /** @var \Illuminate\Contracts\View\View $view */
        $view = view('metabase::embed.show', compact('id', 'params'));
        return $view;
    }
}
