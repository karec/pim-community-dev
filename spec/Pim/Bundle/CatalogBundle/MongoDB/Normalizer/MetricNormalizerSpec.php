<?php

namespace spec\Pim\Bundle\CatalogBundle\MongoDB\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Pim\Bundle\CatalogBundle\Model\Metric;

class MetricNormalizerSpec extends ObjectBehavior
{
    function it_is_a_normalizer()
    {
        $this->shouldImplement('Symfony\Component\Serializer\Normalizer\NormalizerInterface');
    }

    function it_supports_normalization_in_mongodb_json_of_metric(Metric $metric)
    {
        $this->supportsNormalization($metric, 'mongodb_json')->shouldBe(true);
        $this->supportsNormalization($metric, 'json')->shouldBe(false);
        $this->supportsNormalization($metric, 'xml')->shouldBe(false);
    }

    function it_normalizes_metric_when_has_data(Metric $metric)
    {
        $metric->getData()->willReturn(42.55);
        $metric->getUnit()->willReturn('GRAM');
        $metric->getBaseData()->willReturn(0.04255);
        $metric->getBaseUnit()->willReturn('KILOGRAM');
        $metric->getFamily()->willReturn('Weight');

        $this->normalize($metric, 'mongodb_json', [])->shouldReturn(
            [
            'data' => 42.55,
            'unit' => 'GRAM',
            'baseData' => 0.04255,
            'baseUnit' => 'KILOGRAM',
            'family' => 'Weight'
            ]
        );
    }

    function it_normalizes_metric_when_has_no_data(Metric $metric)
    {
        $metric->getData()->willReturn(null);

        $this->normalize($metric, 'mongodb_json', [])->shouldReturn(null);
    }
}
