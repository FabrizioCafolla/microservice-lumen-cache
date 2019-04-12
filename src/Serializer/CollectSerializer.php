<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 22/10/18
	 * Time: 19.38
	 */

	namespace CacheSystem\Serializer;

    use CacheSystem\Serializer\Abstracts\Serializer;
    use CacheSystem\Serializer\Interfaces\SerializerInterface;
    use Illuminate\Support\Collection;

	class CollectSerializer extends Serializer implements SerializerInterface
	{
		const SERIALIZER = CollectSerializer::class;

		public function make($data): ?string
		{
			if (!($data instanceof Collection))
				throw new \Exception("Data is not instance of Collection");

			return $this->_serialize($data, false, self::SERIALIZER);
		}

		public function get($rawData)
		{
			$data = $this->_unserialize($rawData,'data');
			return collect($data['data']);
		}
	}