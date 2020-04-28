<?php

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\Resources\config;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use Cocur\Slugify\SlugifyInterface;
use GuzzleHttp\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Liip\ImagineBundle\Binary\Loader\FileSystemLoader;
use Liip\ImagineBundle\Binary\MimeTypeGuesserInterface;
use Liip\ImagineBundle\Service\FilterService;
use Silverback\ApiComponentBundle\Doctrine\Extension\DiscriminatorMappingExtension;
use Silverback\ApiComponentBundle\Doctrine\Extension\TablePrefixExtension;
use Silverback\ApiComponentBundle\EventSubscriber\DoctrineSubscriber;
use Silverback\ApiComponentBundle\Repository\Route\RouteRepository;
use Silverback\ApiComponentBundle\Security\EventListener\DenyAccessListener;
use Silverback\ApiComponentBundle\Security\EventListener\PublishableConfigurator;
use Silverback\ApiComponentBundle\Security\RestrictedResourceVoter;
use Silverback\ApiComponentBundle\Security\TokenAuthenticator;
use Silverback\ApiComponentBundle\Serializer\ApiContextBuilder;
use Silverback\ApiComponentBundle\Serializer\ApiNormalizer;
use Silverback\ApiComponentBundle\Swagger\SwaggerDecorator;
use Silverback\ApiComponentBundle\Validator\Constraints\ComponentLocationValidator;
use Silverback\ApiComponentBundle\Validator\Constraints\FormHandlerClassValidator;
use Silverback\ApiComponentBundle\Validator\Constraints\FormTypeClassValidator;
use Silverback\ApiComponentBundle\Validator\Constraints\LinkValidator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Twig\Environment;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private()
        ->bind('$formHandlers', new TaggedIteratorArgument('silverback_api_component.form_handler'))
        // ->bind('$projectDir', '%kernel.project_dir%')
        ->bind('$fromEmailAddress', '%env(FROM_EMAIL_ADDRESS)%');

    $services
        ->load('Silverback\\ApiComponentBundle\\', '../../*')
        ->exclude('../../{Entity,Exception,Event,Migrations,Resources,Tests,Dto,DTO,Doctrine/Extension}');

    $services
        ->set(TablePrefixExtension::class)
        ->set(TablePrefixExtension::class)
        ->tag('doctrine.event_listener', [ 'event' => 'loadClassMetadata' ])
    ;

    $services
        ->set(DiscriminatorMappingExtension::class)
        ->tag('doctrine.event_listener', [ 'event' => 'loadClassMetadata' ])
    ;

    $services
        ->load('Silverback\\ApiComponentBundle\\Controller\\', '../../Controller')
        ->tag('controller.service_arguments');

    $services
        ->load('Silverback\\ApiComponentBundle\\EventSubscriber\\EntitySubscriber\\', '../../EventSubscriber/EntitySubscriber')
        ->tag('silverback_api_component.entity_subscriber');

    $services
        ->set(DoctrineSubscriber::class)
        ->tag('doctrine.event_subscriber')
        ->args(
            [
                '$entitySubscribers' => new TaggedIteratorArgument('silverback_api_component.entity_subscriber')
            ]
        );

    $services
        ->set(FormHandlerClassValidator::class)
        ->tag('validator.constraint_validator');

    $services
        ->set(ComponentLocationValidator::class)
        ->tag('validator.constraint_validator');

    $services
        ->set(FormTypeClassValidator::class)
        ->tag('validator.constraint_validator')
        ->args(
            [
                '$formTypes' => new TaggedIteratorArgument('silverback_api_component.form_type')
            ]
        );

    $services
        ->set(LinkValidator::class)
        ->tag('validator.constraint_validator')
        ->args(
            [
                '$routeRepository' => RouteRepository::class
            ]
        );

    $services
        ->load('Silverback\\ApiComponentBundle\\DataProvider\\Item\\', '../../DataProvider/Item')
        ->tag('api_platform.item_data_provider', ['priority' => 1])
        ->autoconfigure(false);

    $services
        ->set(ApiContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder')
        ->args([
            new Reference(ApiContextBuilder::class . '.inner')
        ])
    ;

    $services
        ->set(ApiNormalizer::class)
        ->args([
            tagged_iterator('silverback_api_component.data_transformer'),
            new Reference(RestrictedResourceVoter::class)
        ])
        ->tag('serializer.normalizer', [ 'priority' => 100 ])
    ;
    $services
        ->set(SwaggerDecorator::class)
        ->decorate('api_platform.swagger.normalizer.documentation')
        ->autoconfigure(false)
        ->args(
            [
                new Reference(SwaggerDecorator::class . '.inner')
            ]
        )
    ;


    $services
        ->set(TokenAuthenticator::class)
        ->arg('$tokens', ['%env(VARNISH_TOKEN)%']);

    $services
        ->set(DateTimeNormalizer::class)
        ->arg('$defaultContext', ['datetime_format' => 'Y-m-d H:i:s'])
    ;

    $services
        ->set(PublishableConfigurator::class)
        ->tag('kernel.event_listener', ['event' => 'kernel.request', 'priority' => EventPriorities::PRE_READ])
        ->autoconfigure(false)
    ;

    $services
        ->set(DenyAccessListener::class)
        ->tag('kernel.event_listener', ['event' => 'kernel.view', 'priority' => EventPriorities::POST_SERIALIZE, 'method' => 'onSecurity'])
        ->autoconfigure(false)
    ;

    $services->set(Client::class); // create guzzle client as a service
    $services->alias(SlugifyInterface::class, 'slugify');
    $services->alias(ContextAwareCollectionDataProviderInterface::class, 'api_platform.collection_data_provider');
    $services->alias(JWTManager::class, 'lexik_jwt_authentication.jwt_manager');

    // Support twig minimum stability - autowiring is required with type hinted param
    // Twig bundle 3.4.0 is minimum - 4.3.0 current and recommended at time of writing this note
    $services->alias(Environment::class, 'twig');
    $services->alias(RoleHierarchy::class, 'security.role_hierarchy');
    $services->alias(OperationPathResolverInterface::class, 'api_platform.operation_path_resolver.router');

    $services->alias(FileSystemLoader::class, 'liip_imagine.binary.loader.default');
    $services->alias(FilterService::class, 'liip_imagine.service.filter');
    $services->alias(MimeTypeGuesserInterface::class, 'liip_imagine.binary.mime_type_guesser');
};
