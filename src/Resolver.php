<?php

namespace Bref\LayerArns;

use Composer\InstalledVersions;
use RuntimeException;

class Resolver
{
    public static function resolve(array $layers, string $region): array
    {
        $packageMap = [
            'bref/bref' => '534081306603',
            'bref/extra-php-extensions' => '403367587399'
        ];

        $dataSources = [];

        foreach ($packageMap as $package => $accountId) {
            if (!InstalledVersions::isInstalled($package)) {
                continue;
            }

            $installPath = InstalledVersions::getInstallPath($package);
            if (!$installPath) {
                continue;
            }

            $jsonPath = $installPath . '/layers.json';
            if (!file_exists($jsonPath)) {
                continue;
            }

            $json = json_decode(file_get_contents($jsonPath), true);
            if (!is_array($json)) {
                continue;
            }

            foreach ($json as $layer => $regions) {
                if (!is_array($regions)) continue;
                $dataSources[$layer] = [
                    'regions' => $regions,
                    'account_id' => $accountId,
                ];
            }
        }

        // Add insights layer info from local layers-insights.json
        $insightsJson = __DIR__ . '/../layers-insights.json';
        if (file_exists($insightsJson)) {
            $json = json_decode(file_get_contents($insightsJson), true);
            if (is_array($json)) {
                foreach ($json as $layer => $regions) {
                    if (!is_array($regions)) continue;
                    $dataSources[$layer] = [
                        'regions' => $regions,
                        'account_id' => '580247275435',
                    ];
                }
            }
        }

        $arns = [];
        foreach ($layers as $layer) {
            if (empty($dataSources[$layer]['regions'][$region])) {
                throw new \RuntimeException("Layer '$layer' not found for region '$region'");
            }

            $version = $dataSources[$layer]['regions'][$region];
            $accountId = $dataSources[$layer]['account_id'];
            $awsLayerName = match ($layer) {
                'insights' => 'LambdaInsightsExtension',
                'arm-insights' => 'LambdaInsightsExtension-Arm64',
                default => $layer,
            };
            $arns[] = "arn:aws:lambda:$region:$accountId:layer:$awsLayerName:$version";
        }

        return $arns;
    }
}
