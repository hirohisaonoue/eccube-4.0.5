<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'console.command_loader' shared service.

include_once $this->targetDirs[3].'\\vendor\\symfony\\console\\CommandLoader\\CommandLoaderInterface.php';
include_once $this->targetDirs[3].'\\vendor\\symfony\\console\\CommandLoader\\ContainerCommandLoader.php';

return $this->services['console.command_loader'] = new \Symfony\Component\Console\CommandLoader\ContainerCommandLoader(new \Symfony\Component\DependencyInjection\ServiceLocator(['Eccube\\Command\\ComposerInstallCommand' => function () {
    $f = function (\Eccube\Command\ComposerInstallCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\ComposerInstallCommand']) ? $this->services['Eccube\\Command\\ComposerInstallCommand'] : $this->load('getComposerInstallCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\ComposerRemoveCommand' => function () {
    $f = function (\Eccube\Command\ComposerRemoveCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\ComposerRemoveCommand']) ? $this->services['Eccube\\Command\\ComposerRemoveCommand'] : $this->load('getComposerRemoveCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\ComposerRequireAlreadyInstalledPluginsCommand' => function () {
    $f = function (\Eccube\Command\ComposerRequireAlreadyInstalledPluginsCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\ComposerRequireAlreadyInstalledPluginsCommand']) ? $this->services['Eccube\\Command\\ComposerRequireAlreadyInstalledPluginsCommand'] : $this->load('getComposerRequireAlreadyInstalledPluginsCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\ComposerRequireCommand' => function () {
    $f = function (\Eccube\Command\ComposerRequireCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\ComposerRequireCommand']) ? $this->services['Eccube\\Command\\ComposerRequireCommand'] : $this->load('getComposerRequireCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\ComposerUpdateCommand' => function () {
    $f = function (\Eccube\Command\ComposerUpdateCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\ComposerUpdateCommand']) ? $this->services['Eccube\\Command\\ComposerUpdateCommand'] : $this->load('getComposerUpdateCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\DeleteCartsCommand' => function () {
    $f = function (\Eccube\Command\DeleteCartsCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\DeleteCartsCommand']) ? $this->services['Eccube\\Command\\DeleteCartsCommand'] : $this->load('getDeleteCartsCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\GenerateDummyDataCommand' => function () {
    $f = function (\Eccube\Command\GenerateDummyDataCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\GenerateDummyDataCommand']) ? $this->services['Eccube\\Command\\GenerateDummyDataCommand'] : $this->load('getGenerateDummyDataCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\GenerateProxyCommand' => function () {
    $f = function (\Eccube\Command\GenerateProxyCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\GenerateProxyCommand']) ? $this->services['Eccube\\Command\\GenerateProxyCommand'] : $this->load('getGenerateProxyCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\InstallerCommand' => function () {
    $f = function (\Eccube\Command\InstallerCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\InstallerCommand']) ? $this->services['Eccube\\Command\\InstallerCommand'] : $this->load('getInstallerCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\LoadDataFixturesEccubeCommand' => function () {
    $f = function (\Eccube\Command\LoadDataFixturesEccubeCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\LoadDataFixturesEccubeCommand']) ? $this->services['Eccube\\Command\\LoadDataFixturesEccubeCommand'] : $this->load('getLoadDataFixturesEccubeCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\PluginDisableCommand' => function () {
    $f = function (\Eccube\Command\PluginDisableCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\PluginDisableCommand']) ? $this->services['Eccube\\Command\\PluginDisableCommand'] : $this->load('getPluginDisableCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\PluginEnableCommand' => function () {
    $f = function (\Eccube\Command\PluginEnableCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\PluginEnableCommand']) ? $this->services['Eccube\\Command\\PluginEnableCommand'] : $this->load('getPluginEnableCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\PluginGenerateCommand' => function () {
    $f = function (\Eccube\Command\PluginGenerateCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\PluginGenerateCommand']) ? $this->services['Eccube\\Command\\PluginGenerateCommand'] : $this->load('getPluginGenerateCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\PluginInstallCommand' => function () {
    $f = function (\Eccube\Command\PluginInstallCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\PluginInstallCommand']) ? $this->services['Eccube\\Command\\PluginInstallCommand'] : $this->load('getPluginInstallCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\PluginSchemaUpdateCommand' => function () {
    $f = function (\Eccube\Command\PluginSchemaUpdateCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\PluginSchemaUpdateCommand']) ? $this->services['Eccube\\Command\\PluginSchemaUpdateCommand'] : $this->load('getPluginSchemaUpdateCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\PluginUninstallCommand' => function () {
    $f = function (\Eccube\Command\PluginUninstallCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\PluginUninstallCommand']) ? $this->services['Eccube\\Command\\PluginUninstallCommand'] : $this->load('getPluginUninstallCommandService.php')) && false ?: '_'});
}, 'Eccube\\Command\\PluginUpdateCommand' => function () {
    $f = function (\Eccube\Command\PluginUpdateCommand $v) { return $v; }; return $f(${($_ = isset($this->services['Eccube\\Command\\PluginUpdateCommand']) ? $this->services['Eccube\\Command\\PluginUpdateCommand'] : $this->load('getPluginUpdateCommandService.php')) && false ?: '_'});
}, 'console.command.about' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\AboutCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.about']) ? $this->services['console.command.about'] : $this->load('getConsole_Command_AboutService.php')) && false ?: '_'});
}, 'console.command.assets_install' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.assets_install']) ? $this->services['console.command.assets_install'] : $this->load('getConsole_Command_AssetsInstallService.php')) && false ?: '_'});
}, 'console.command.cache_clear' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.cache_clear']) ? $this->services['console.command.cache_clear'] : $this->load('getConsole_Command_CacheClearService.php')) && false ?: '_'});
}, 'console.command.cache_pool_clear' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\CachePoolClearCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.cache_pool_clear']) ? $this->services['console.command.cache_pool_clear'] : $this->load('getConsole_Command_CachePoolClearService.php')) && false ?: '_'});
}, 'console.command.cache_pool_prune' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\CachePoolPruneCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.cache_pool_prune']) ? $this->services['console.command.cache_pool_prune'] : $this->load('getConsole_Command_CachePoolPruneService.php')) && false ?: '_'});
}, 'console.command.cache_warmup' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\CacheWarmupCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.cache_warmup']) ? $this->services['console.command.cache_warmup'] : $this->load('getConsole_Command_CacheWarmupService.php')) && false ?: '_'});
}, 'console.command.config_debug' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\ConfigDebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.config_debug']) ? $this->services['console.command.config_debug'] : $this->load('getConsole_Command_ConfigDebugService.php')) && false ?: '_'});
}, 'console.command.config_dump_reference' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\ConfigDumpReferenceCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.config_dump_reference']) ? $this->services['console.command.config_dump_reference'] : $this->load('getConsole_Command_ConfigDumpReferenceService.php')) && false ?: '_'});
}, 'console.command.container_debug' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\ContainerDebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.container_debug']) ? $this->services['console.command.container_debug'] : $this->load('getConsole_Command_ContainerDebugService.php')) && false ?: '_'});
}, 'console.command.debug_autowiring' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\DebugAutowiringCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.debug_autowiring']) ? $this->services['console.command.debug_autowiring'] : $this->load('getConsole_Command_DebugAutowiringService.php')) && false ?: '_'});
}, 'console.command.event_dispatcher_debug' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\EventDispatcherDebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.event_dispatcher_debug']) ? $this->services['console.command.event_dispatcher_debug'] : $this->load('getConsole_Command_EventDispatcherDebugService.php')) && false ?: '_'});
}, 'console.command.form_debug' => function () {
    $f = function (\Symfony\Component\Form\Command\DebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.form_debug']) ? $this->services['console.command.form_debug'] : $this->load('getConsole_Command_FormDebugService.php')) && false ?: '_'});
}, 'console.command.router_debug' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.router_debug']) ? $this->services['console.command.router_debug'] : $this->load('getConsole_Command_RouterDebugService.php')) && false ?: '_'});
}, 'console.command.router_match' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\RouterMatchCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.router_match']) ? $this->services['console.command.router_match'] : $this->load('getConsole_Command_RouterMatchService.php')) && false ?: '_'});
}, 'console.command.translation_debug' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\TranslationDebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.translation_debug']) ? $this->services['console.command.translation_debug'] : $this->load('getConsole_Command_TranslationDebugService.php')) && false ?: '_'});
}, 'console.command.translation_update' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\TranslationUpdateCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.translation_update']) ? $this->services['console.command.translation_update'] : $this->load('getConsole_Command_TranslationUpdateService.php')) && false ?: '_'});
}, 'console.command.workflow_dump' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\WorkflowDumpCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.workflow_dump']) ? $this->services['console.command.workflow_dump'] : $this->load('getConsole_Command_WorkflowDumpService.php')) && false ?: '_'});
}, 'console.command.xliff_lint' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\XliffLintCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.xliff_lint']) ? $this->services['console.command.xliff_lint'] : $this->load('getConsole_Command_XliffLintService.php')) && false ?: '_'});
}, 'console.command.yaml_lint' => function () {
    $f = function (\Symfony\Bundle\FrameworkBundle\Command\YamlLintCommand $v) { return $v; }; return $f(${($_ = isset($this->services['console.command.yaml_lint']) ? $this->services['console.command.yaml_lint'] : $this->load('getConsole_Command_YamlLintService.php')) && false ?: '_'});
}, 'doctrine.cache_clear_metadata_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\ClearMetadataCacheDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.cache_clear_metadata_command']) ? $this->services['doctrine.cache_clear_metadata_command'] : $this->load('getDoctrine_CacheClearMetadataCommandService.php')) && false ?: '_'});
}, 'doctrine.cache_clear_query_cache_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\ClearQueryCacheDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.cache_clear_query_cache_command']) ? $this->services['doctrine.cache_clear_query_cache_command'] : $this->load('getDoctrine_CacheClearQueryCacheCommandService.php')) && false ?: '_'});
}, 'doctrine.cache_clear_result_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\ClearResultCacheDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.cache_clear_result_command']) ? $this->services['doctrine.cache_clear_result_command'] : $this->load('getDoctrine_CacheClearResultCommandService.php')) && false ?: '_'});
}, 'doctrine.cache_collection_region_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\CollectionRegionDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.cache_collection_region_command']) ? $this->services['doctrine.cache_collection_region_command'] : $this->load('getDoctrine_CacheCollectionRegionCommandService.php')) && false ?: '_'});
}, 'doctrine.clear_entity_region_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\EntityRegionCacheDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.clear_entity_region_command']) ? $this->services['doctrine.clear_entity_region_command'] : $this->load('getDoctrine_ClearEntityRegionCommandService.php')) && false ?: '_'});
}, 'doctrine.clear_query_region_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\QueryRegionCacheDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.clear_query_region_command']) ? $this->services['doctrine.clear_query_region_command'] : $this->load('getDoctrine_ClearQueryRegionCommandService.php')) && false ?: '_'});
}, 'doctrine.database_create_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.database_create_command']) ? $this->services['doctrine.database_create_command'] : $this->load('getDoctrine_DatabaseCreateCommandService.php')) && false ?: '_'});
}, 'doctrine.database_drop_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.database_drop_command']) ? $this->services['doctrine.database_drop_command'] : $this->load('getDoctrine_DatabaseDropCommandService.php')) && false ?: '_'});
}, 'doctrine.database_import_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\ImportDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.database_import_command']) ? $this->services['doctrine.database_import_command'] : $this->load('getDoctrine_DatabaseImportCommandService.php')) && false ?: '_'});
}, 'doctrine.ensure_production_settings_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\EnsureProductionSettingsDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.ensure_production_settings_command']) ? $this->services['doctrine.ensure_production_settings_command'] : $this->load('getDoctrine_EnsureProductionSettingsCommandService.php')) && false ?: '_'});
}, 'doctrine.fixtures_load_command' => function () {
    $f = function (\Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.fixtures_load_command']) ? $this->services['doctrine.fixtures_load_command'] : $this->load('getDoctrine_FixturesLoadCommandService.php')) && false ?: '_'});
}, 'doctrine.generate_entities_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\GenerateEntitiesDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.generate_entities_command']) ? $this->services['doctrine.generate_entities_command'] : $this->load('getDoctrine_GenerateEntitiesCommandService.php')) && false ?: '_'});
}, 'doctrine.mapping_convert_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\ConvertMappingDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.mapping_convert_command']) ? $this->services['doctrine.mapping_convert_command'] : $this->load('getDoctrine_MappingConvertCommandService.php')) && false ?: '_'});
}, 'doctrine.mapping_import_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\ImportMappingDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.mapping_import_command']) ? $this->services['doctrine.mapping_import_command'] : $this->load('getDoctrine_MappingImportCommandService.php')) && false ?: '_'});
}, 'doctrine.mapping_info_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\InfoDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.mapping_info_command']) ? $this->services['doctrine.mapping_info_command'] : $this->load('getDoctrine_MappingInfoCommandService.php')) && false ?: '_'});
}, 'doctrine.query_dql_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunDqlDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.query_dql_command']) ? $this->services['doctrine.query_dql_command'] : $this->load('getDoctrine_QueryDqlCommandService.php')) && false ?: '_'});
}, 'doctrine.query_sql_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.query_sql_command']) ? $this->services['doctrine.query_sql_command'] : $this->load('getDoctrine_QuerySqlCommandService.php')) && false ?: '_'});
}, 'doctrine.schema_create_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.schema_create_command']) ? $this->services['doctrine.schema_create_command'] : $this->load('getDoctrine_SchemaCreateCommandService.php')) && false ?: '_'});
}, 'doctrine.schema_drop_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\DropSchemaDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.schema_drop_command']) ? $this->services['doctrine.schema_drop_command'] : $this->load('getDoctrine_SchemaDropCommandService.php')) && false ?: '_'});
}, 'doctrine.schema_update_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.schema_update_command']) ? $this->services['doctrine.schema_update_command'] : $this->load('getDoctrine_SchemaUpdateCommandService.php')) && false ?: '_'});
}, 'doctrine.schema_validate_command' => function () {
    $f = function (\Doctrine\Bundle\DoctrineBundle\Command\Proxy\ValidateSchemaCommand $v) { return $v; }; return $f(${($_ = isset($this->services['doctrine.schema_validate_command']) ? $this->services['doctrine.schema_validate_command'] : $this->load('getDoctrine_SchemaValidateCommandService.php')) && false ?: '_'});
}, 'security.command.user_password_encoder' => function () {
    $f = function (\Symfony\Bundle\SecurityBundle\Command\UserPasswordEncoderCommand $v) { return $v; }; return $f(${($_ = isset($this->services['security.command.user_password_encoder']) ? $this->services['security.command.user_password_encoder'] : $this->load('getSecurity_Command_UserPasswordEncoderService.php')) && false ?: '_'});
}, 'swiftmailer.command.debug' => function () {
    $f = function (\Symfony\Bundle\SwiftmailerBundle\Command\DebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['swiftmailer.command.debug']) ? $this->services['swiftmailer.command.debug'] : $this->load('getSwiftmailer_Command_DebugService.php')) && false ?: '_'});
}, 'swiftmailer.command.new_email' => function () {
    $f = function (\Symfony\Bundle\SwiftmailerBundle\Command\NewEmailCommand $v) { return $v; }; return $f(${($_ = isset($this->services['swiftmailer.command.new_email']) ? $this->services['swiftmailer.command.new_email'] : $this->load('getSwiftmailer_Command_NewEmailService.php')) && false ?: '_'});
}, 'swiftmailer.command.send_email' => function () {
    $f = function (\Symfony\Bundle\SwiftmailerBundle\Command\SendEmailCommand $v) { return $v; }; return $f(${($_ = isset($this->services['swiftmailer.command.send_email']) ? $this->services['swiftmailer.command.send_email'] : $this->load('getSwiftmailer_Command_SendEmailService.php')) && false ?: '_'});
}, 'twig.command.debug' => function () {
    $f = function (\Symfony\Bridge\Twig\Command\DebugCommand $v) { return $v; }; return $f(${($_ = isset($this->services['twig.command.debug']) ? $this->services['twig.command.debug'] : $this->load('getTwig_Command_DebugService.php')) && false ?: '_'});
}, 'twig.command.lint' => function () {
    $f = function (\Symfony\Bundle\TwigBundle\Command\LintCommand $v) { return $v; }; return $f(${($_ = isset($this->services['twig.command.lint']) ? $this->services['twig.command.lint'] : $this->load('getTwig_Command_LintService.php')) && false ?: '_'});
}]), ['eccube:composer:install' => 'Eccube\\Command\\ComposerInstallCommand', 'eccube:composer:remove' => 'Eccube\\Command\\ComposerRemoveCommand', 'eccube:composer:require-already-installed' => 'Eccube\\Command\\ComposerRequireAlreadyInstalledPluginsCommand', 'eccube:composer:require' => 'Eccube\\Command\\ComposerRequireCommand', 'eccube:composer:update' => 'Eccube\\Command\\ComposerUpdateCommand', 'eccube:delete-carts' => 'Eccube\\Command\\DeleteCartsCommand', 'eccube:fixtures:generate' => 'Eccube\\Command\\GenerateDummyDataCommand', 'eccube:generate:proxies' => 'Eccube\\Command\\GenerateProxyCommand', 'eccube:install' => 'Eccube\\Command\\InstallerCommand', 'eccube:fixtures:load' => 'Eccube\\Command\\LoadDataFixturesEccubeCommand', 'eccube:plugin:disable' => 'Eccube\\Command\\PluginDisableCommand', 'eccube:plugin:enable' => 'Eccube\\Command\\PluginEnableCommand', 'eccube:plugin:generate' => 'Eccube\\Command\\PluginGenerateCommand', 'eccube:plugin:install' => 'Eccube\\Command\\PluginInstallCommand', 'eccube:plugin:schema-update' => 'Eccube\\Command\\PluginSchemaUpdateCommand', 'eccube:plugin:uninstall' => 'Eccube\\Command\\PluginUninstallCommand', 'eccube:plugin:update' => 'Eccube\\Command\\PluginUpdateCommand', 'about' => 'console.command.about', 'assets:install' => 'console.command.assets_install', 'cache:clear' => 'console.command.cache_clear', 'cache:pool:clear' => 'console.command.cache_pool_clear', 'cache:pool:prune' => 'console.command.cache_pool_prune', 'cache:warmup' => 'console.command.cache_warmup', 'debug:config' => 'console.command.config_debug', 'config:dump-reference' => 'console.command.config_dump_reference', 'debug:container' => 'console.command.container_debug', 'debug:autowiring' => 'console.command.debug_autowiring', 'debug:event-dispatcher' => 'console.command.event_dispatcher_debug', 'debug:router' => 'console.command.router_debug', 'router:match' => 'console.command.router_match', 'debug:translation' => 'console.command.translation_debug', 'translation:update' => 'console.command.translation_update', 'workflow:dump' => 'console.command.workflow_dump', 'lint:xliff' => 'console.command.xliff_lint', 'lint:yaml' => 'console.command.yaml_lint', 'debug:form' => 'console.command.form_debug', 'security:encode-password' => 'security.command.user_password_encoder', 'doctrine:database:create' => 'doctrine.database_create_command', 'doctrine:database:drop' => 'doctrine.database_drop_command', 'doctrine:database:import' => 'doctrine.database_import_command', 'doctrine:generate:entities' => 'doctrine.generate_entities_command', 'doctrine:query:sql' => 'doctrine.query_sql_command', 'doctrine:cache:clear-metadata' => 'doctrine.cache_clear_metadata_command', 'doctrine:cache:clear-query' => 'doctrine.cache_clear_query_cache_command', 'doctrine:cache:clear-result' => 'doctrine.cache_clear_result_command', 'doctrine:cache:clear-collection-region' => 'doctrine.cache_collection_region_command', 'doctrine:mapping:convert' => 'doctrine.mapping_convert_command', 'doctrine:schema:create' => 'doctrine.schema_create_command', 'doctrine:schema:drop' => 'doctrine.schema_drop_command', 'doctrine:ensure-production-settings' => 'doctrine.ensure_production_settings_command', 'doctrine:cache:clear-entity-region' => 'doctrine.clear_entity_region_command', 'doctrine:mapping:info' => 'doctrine.mapping_info_command', 'doctrine:cache:clear-query-region' => 'doctrine.clear_query_region_command', 'doctrine:query:dql' => 'doctrine.query_dql_command', 'doctrine:schema:update' => 'doctrine.schema_update_command', 'doctrine:schema:validate' => 'doctrine.schema_validate_command', 'doctrine:mapping:import' => 'doctrine.mapping_import_command', 'doctrine:fixtures:load' => 'doctrine.fixtures_load_command', 'debug:swiftmailer' => 'swiftmailer.command.debug', 'swiftmailer:email:send' => 'swiftmailer.command.new_email', 'swiftmailer:spool:send' => 'swiftmailer.command.send_email', 'debug:twig' => 'twig.command.debug', 'lint:twig' => 'twig.command.lint']);
