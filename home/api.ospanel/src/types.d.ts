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
