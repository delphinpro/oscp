/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

type DomainConfigData = {
    aliases: string
    enabled: boolean
    environment: string
    ip: string
    nginx_engine: string
    node_engine: string
    php_engine: string
    project_dir: string
    public_dir: string
    project_url: string
    ssl: boolean
    ssl_cert_file: string
    ssl_key_file: string
    start_command: string
    terminal_codepage: string

    admin_path: string
};

type DomainComputedData = {
    aliases: string
    enabled: boolean
    environment: string
    ip: string
    nginx_engine: string
    node_engine: string
    php_engine: string
    project_dir: string
    public_dir: string
    project_url: string
    ssl: boolean
    ssl_cert_file: string
    ssl_key_file: string
    start_command: string
    terminal_codepage: string

    base_dir: string
    host_aliases: Array<string>
    host_modules: Array<string>
    realhost: string
    webhost: string
    // defected: boolean
    // dip: string
    // tag: string
};

type Domain = {
    host: string
    isActive: boolean
    isProblem: boolean
    isDisabled: boolean
    adminUrl: string
    siteUrl: string
    isValidRoot: boolean
    isAvailable: boolean
    isReadyPhpEngine: boolean
    isReadyNginxEngine: boolean

    config: DomainConfigData
    computed: DomainComputedData
}

type Module = {
    name: string
    alt_name: string
    status: string
    enabled: boolean
    init: boolean
    version: string
    arch: string
    category: string

    ip: string | null
    port: string | null
}

type MainSettings = {
    api_domain: string
    api_ip: string
    api_port: number
    clear_dns_cache: boolean
    lang: string
    log_max_filesize: number
    projects_search_depth: number
    projects_search_path: string
    task_scheduler: boolean
    terminal_ansi_fix: string
    update_check: boolean
    use_hosts_file: boolean
    use_win_terminal: boolean
}

type MenuSettings = {
    do_not_group_single_item: boolean
    show_icons: boolean
    show_addons: boolean
    show_addons_in_groups: boolean
    show_addons_in_submenu: boolean
    show_modules: boolean
    show_modules_in_groups: boolean
    show_modules_in_submenu: boolean
    show_projects: boolean
    show_projects_in_groups: boolean
    show_projects_in_submenu: boolean
    show_tray_icon: boolean
    show_hr_after_addons: boolean
    show_hr_after_modules: boolean
    show_hr_after_projects: boolean
}

type ProjectsSettings = {
    aliases: string
    enabled: boolean
    environment: string
    ip: string
    nginx_engine: string
    node_engine: string
    php_engine: string
    project_dir: string
    project_url: string
    public_dir: string
    ssl: boolean
    ssl_cert_file: string
    ssl_key_file: string
    start_command: string
    terminal_codepage: string
}

type ModulesSettings = {
    allowed_system_env_vars: string
    log_max_filesize: number
    log_write_title: boolean
    max_probation_fails: number
    max_shutdown_time: number
    probation: number
    silent_mode: boolean
    terminal_codepage: number
    time_zone: string
    used_addons_environment: string
}

type SmtpSettings = {
    open_email_after_saving: boolean
    saved_email_extension: string
    smtp_port: number
    smtp_server: boolean
}

type Settings = {
    main: MainSettings
    menu: MenuSettings
    projects: ProjectsSettings
    modules: ModulesSettings
    smtp: SmtpSettings
}
