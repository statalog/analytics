<?php

return [

    // Pages
    'page_index'  => 'Sites',
    'page_create' => 'Adicionar Site',
    'page_show'   => 'Detalhes do Site',
    'page_edit'   => 'Editar Site',

    // Fields
    'field_name'             => 'Nome do Site',
    'field_domain'           => 'Domínio',
    'field_timezone'         => 'Fuso Horário',
    'field_track_subdomains' => 'Rastrear subdomínios',
    'field_is_active'        => 'Ativo',
    'field_site_id'          => 'ID do Site',

    // Hints
    'hint_domain'           => 'Seu domínio de site, sem http:// (ex: exemplo.com).',
    'hint_track_subdomains' => 'Quando ativado, acessos de qualquer subdomínio serão registrados.',
    'hint_timezone'         => 'Usado para agrupar estatísticas por dia no seu fuso horário local.',

    // Tracking
    'tracking_snippet_title' => 'Script de Rastreamento',
    'tracking_snippet_hint'  => 'Copie este trecho para o <head> do seu site.',
    'tracking_copy'          => 'Copiar para área de transferência',
    'tracking_copied'        => 'Copiado!',
    'tracking_copy_code'     => 'Copiar código',

    // Bot tracking
    'bot_track_label'    => 'Armazenar tráfego de bots',
    'bot_track_recommended' => 'Recomendado',
    'bot_track_hint'     => 'Altamente recomendado — rastreie crawlers (Googlebot, Bingbot, scrapers de IA como GPTBot e ClaudeBot, ferramentas SEO, etc.) para entender quem está indexando e mineirando seu site. Acessos de bots são sempre excluídos de suas estatísticas regulares por padrão, então nunca poluem sua análise humana. Você obtém uma página dedicada de Bots com uma análise por bot, categoria e página, e pode alternar bots dentro ou fora em qualquer relatório.',
    'bot_detection_label' => 'Detecção de bot é executada automaticamente',
    'bot_detection_hint'  => 'O Statalog identifica crawlers de mecanismo de busca (Googlebot, Bingbot), scrapers de IA (GPTBot, ClaudeBot, PerplexityBot) e ferramentas SEO usando User-Agent verificado e assinaturas IP. Acessos de bots nunca contam para suas visualizações de página faturáveis e são excluídos de estatísticas humanas — aparecem na página dedicada de Bots.',
    'bot_snippet_title'  => 'Trecho de rastreamento de bots',
    'bot_snippet_intro'  => 'Você tem Armazenar tráfego de bots ativado. Substitua seu trecho de rastreamento atual por este. Ele adiciona um pixel <code>&lt;noscript&gt;</code> que crawlers (Googlebot, GPTBot, etc.) buscam automaticamente — a URL da página é detectada a partir da solicitação sem necessidade de configuração extra.',
    'bot_snippet_note'   => 'Cole isto uma vez no layout global do seu site. Sem placeholders para substituir.',

    // Public dashboard
    'public_title'           => 'Painel Público',
    'public_enable'          => 'Tornar este painel acessível publicamente',
    'public_password_label'  => 'Proteger com senha (opcional)',
    'public_password_placeholder' => 'Deixe em branco para manter a senha atual',
    'public_url'             => 'Link compartilhável',
    'public_sections'        => 'Seções a mostrar',

    // Section labels
    'section_chart'       => 'Gráfico',
    'section_pages'       => 'Principais Páginas',
    'section_sources'     => 'Fontes de Tráfego',
    'section_locations'   => 'Locais',
    'section_devices'     => 'Dispositivos',
    'section_browsers'    => 'Navegadores',
    'section_os'          => 'Sistemas Operacionais',
    'section_resolutions' => 'Resoluções de Tela',

    // Stats on index
    'stats_today'      => 'Hoje',
    'stats_this_month' => 'Este mês',
    'stats_last_month' => 'Mês passado',
    'stats_hits'       => 'acessos',

    // Stats cards on index
    'card_total_visitors'  => 'Total de Visitantes',
    'card_total_sessions'  => 'Total de Sessões',
    'card_total_pageviews' => 'Total de Visualizações de Página',
    'card_sites_tracked'   => 'Sites Rastreados',
    'vs_previous'          => 'vs anterior',
    'your_websites'        => 'Seus Sites',
    'visitors'             => 'Visitantes',
    'sessions'             => 'Sessões',
    'pageviews'            => 'Visualizações de Página',
    'tracking_paused'      => 'Rastreamento pausado',
    'site_settings'        => 'Configurações do site',
    'open_dashboard'       => 'Abrir painel :site',

    // Plan usage
    'plan_usage'         => 'Uso do plano',
    'billing_period'     => 'Período de faturamento: :from – :to',
    'day_of_total'       => 'Dia :elapsed de :total',
    'pageviews_label'    => 'visualizações de página',
    'percent_used'       => ':percent% usado',
    'unlimited'          => 'Ilimitado',
    'resets'             => 'Reseta :date',

    // Show page
    'website_details'        => 'Detalhes do Site',
    'delete_website_title'   => 'Excluir site?',
    'delete_website_warn'    => 'Você está prestes a excluir permanentemente :name e todos os seus dados analíticos (visualizações de página, eventos, erros, mapas de calor). Isso não pode ser desfeito.',
    'confirm_password_label' => 'Confirme sua senha',
    'confirm_password_placeholder' => 'Sua senha da conta',

    // Buttons
    'btn_add_site'    => 'Adicionar Site',
    'btn_save'        => 'Salvar Site',
    'btn_delete'      => 'Excluir Site',
    'btn_view_stats'  => 'Ver Estatísticas',

    // Confirm
    'confirm_delete' => 'Você tem certeza de que deseja excluir este site? Todos os dados analíticos serão mantidos no ClickHouse.',

    // Empty states
    'no_sites'        => 'Nenhum site adicionado ainda.',
    'no_sites_cta'    => 'Adicione seu primeiro site para começar a rastrear.',

    // Success messages
    'msg_added'   => 'Site adicionado. Copie o script de rastreamento abaixo.',
    'msg_updated' => 'Site atualizado.',
    'msg_removed' => 'Site removido.',

    // Create form placeholders
    'placeholder_name'   => 'Meu Site',
    'placeholder_domain' => 'exemplo.com',

    // Account picker
    'account_picker_title'   => 'Escolher uma conta',
    'account_picker_logged'  => 'Conectado como :email',
    'account_picker_choose_title' => 'Escolher conta',
    'account_your'           => 'Sua conta',
    'account_no_sites_yet'   => 'Nenhum site ainda',
    'account_site_one'       => ':count site',
    'account_site_many'      => ':count sites',
    'btn_logout'             => 'Sair',

];
