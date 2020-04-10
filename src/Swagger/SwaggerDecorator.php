<?php

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var array $docs */
//        $docs = $this->decorated->normalize($object, $format, $context);
//
//        $currentPath = '/forms/{id}';
//        $patchOpPath = $currentPath . '/submit';
//        /** @var \ArrayObject $paths */
//        $paths = $docs['paths'];
//        if (!$paths->offsetExists($patchOpPath)) {
//            return $docs;
//        }
//
//        $patchOp = $docs['paths'][$patchOpPath]['patch'];
//        $patchOp['summary'] = 'Submit a single input for validation';
//        $patchOp['parameters'] = $docs['paths'][$currentPath]['get']['parameters'];
//        $patchOp['parameters'][] = [
//            'name' => 'form',
//            'in' => 'body',
//            'required' => false,
//            'schema' => [
//                'type' => 'object',
//                'properties' => [
//                    'form_name' => [
//                        'type' => 'object',
//                        'properties' => [
//                            'input_name' => [
//                                'type' => 'string'
//                            ]
//                        ]
//                    ]
//                ]
//            ]
//        ];
//        $patchOp['responses'] = $docs['paths'][$currentPath]['get']['responses'];
//        $patchOp['responses']['200']['description'] = 'Validation passed successfully';
//        $patchOp['responses']['400'] = [
//            'description' => 'Validation failed',
//            'schema' => [
//                '$ref' => '#/definitions/Form'
//            ]
//        ];
//        $putData = (array) $docs['paths'][$currentPath]['put'];
//        if (isset($putData['consumes'])) {
//            $patchOp['consumes'] = $putData['consumes'];
//            $patchOp['produces'] = $putData['produces'];
//        }
//
//        $docs['paths'][$patchOpPath]['patch'] = $patchOp;
//        $docs['paths'][$patchOpPath]['post']['parameters'] = $docs['paths'][$patchOpPath]['patch']['parameters'];
//        $docs['paths'][$patchOpPath]['post']['summary'] = 'Submit and validate the entire form';

        return [];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
