/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

type Domain = {
    host: string
    aliases: string
    enabled: boolean
    engine: string
    ip: string
    log_format: string
    cgi_dir: string
    public_dir: string
    project_home_dir: string
    auto_configure: boolean
    ssl: boolean
    ssl_cert_file: string
    ssl_key_file: string
    project_add_modules: string
    project_add_commands: string
    project_use_sys_env: boolean
    admin_path: string

    siteUrl: string
    adminUrl: string
    isValidRoot: boolean
    isAvailable: boolean

    isActive: boolean
    isProblem: boolean
    isDisabled: boolean
}

type Module = {
    name: string
    status: string
    enabled: boolean
    init: boolean
    version: string
    arch: string
    category: string

    ip: string | null
    port: string | null
}
