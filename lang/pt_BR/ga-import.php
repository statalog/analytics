<?php

return [

    // Pages
    'page_index'    => 'Importar do Google Analytics',
    'page_select'   => 'Escolher uma propriedade do GA',
    'page_progress' => 'Importação em andamento',
    'page_summary'  => 'Dados históricos — :site',

    // Index
    'intro' => 'Puxe visualizações de página históricas, visitantes, principais páginas e principais fontes de sua propriedade GA4 para o Statalog. Útil ao migrar do Google Analytics — não perca seus dados anteriores.',

    'oauth_not_configured_title' => 'Google OAuth não configurado',
    'oauth_not_configured_body'  => 'Para habilitar importação do GA, registre um app Web OAuth 2.0 em console.cloud.google.com, ative a Google Analytics Data API e defina o URI de redirecionamento para :redirect. Depois adicione GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET e GOOGLE_REDIRECT_URI ao seu arquivo .env e atualize.',

    'step1_title'      => 'Etapa 1 — Faça login com o Google',
    'step1_body'       => 'Solicitaremos acesso somente leitura ao seu Google Analytics. Nunca vemos os dados da sua conta do Google e você pode desconectar a qualquer momento.',
    'btn_connect'      => 'Conectar com Google',
    'connected_title'  => 'Conectado ao Google Analytics',
    'connected_body'   => 'Escolha uma propriedade GA4 para importar para um de seus sites Statalog.',
    'btn_continue'     => 'Continuar',
    'btn_disconnect'   => 'Desconectar',

    'whats_imported_title' => 'O que é importado',
    'whats_imported_1'     => 'Visitantes diários, visualizações de página, sessões',
    'whats_imported_2'     => 'Taxa de rejeição e duração média',
    'whats_imported_3'     => '50 principais páginas',
    'whats_imported_4'     => '20 principais fontes e países',
    'whats_imported_5'     => 'Dados GA4, até 14 meses',

    'recent_imports'    => 'Importações recentes',
    'col_site'          => 'Site',
    'col_ga_property'   => 'Propriedade GA',
    'col_range'         => 'Período',
    'col_status'        => 'Status',
    'col_progress'      => 'Progresso',
    'days_progress'     => ':processed/:total dias',
    'btn_view'          => 'Visualizar',
    'btn_progress'      => 'Progresso',

    // Select
    'no_properties_title' => 'Nenhuma propriedade GA4 encontrada',
    'no_properties_body'  => 'Certifique-se de que a conta do Google que você conectou tem acesso a pelo menos uma propriedade GA4. Propriedades Universal Analytics não são mais suportadas.',
    'label_property'      => 'Propriedade do Google Analytics',
    'choose_property'     => '— Escolher uma propriedade —',
    'label_target_site'   => 'Importar para site Statalog',
    'choose_site'         => '— Escolher um site —',
    'hint_target_site'    => 'Os dados históricos importados serão anexados a este site Statalog.',
    'label_history'       => 'Quanto histórico',
    'history_1'           => 'Último 1 mês',
    'history_3'           => 'Últimos 3 meses',
    'history_6'           => 'Últimos 6 meses',
    'history_12'          => 'Últimos 12 meses',
    'history_14'          => 'Últimos 14 meses (máximo GA4)',
    'btn_start_import'    => 'Iniciar importação',
    'btn_cancel'          => 'Cancelar',

    // Progress
    'importing'                 => 'Importando do Google Analytics',
    'days_processed'            => ':processed / :total dias processados',
    'btn_view_imported'         => 'Visualizar dados importados',
    'btn_back_to_imports'       => 'Voltar para importações',
    'status_completed'          => 'Concluído',
    'status_failed'             => 'Falhou',
    'importing_percent'         => 'Importando — :percent%',

    // Summary
    'historical_subtitle' => 'Dados históricos importados do Google Analytics',
    'stat_visitors'       => 'Visitantes',
    'stat_pageviews'      => 'Visualizações de Página',
    'stat_sessions'       => 'Sessões',
    'stat_avg_bounce'     => 'Taxa de rejeição média',
    'pageviews_per_day'   => 'Visualizações de página por dia',
    'top_pages'           => 'Principais páginas',
    'top_sources'         => 'Principais fontes',
    'top_countries'       => 'Principais países',
    'col_page'            => 'Página',
    'col_pageviews'       => 'Visualizações de Página',
    'col_source'          => 'Fonte',
    'col_visitors'        => 'Visitantes',
    'col_country'         => 'País',
    'direct'              => '(direto)',
    'unknown'             => 'Desconhecido',
    'no_data'             => 'Sem dados',

];
