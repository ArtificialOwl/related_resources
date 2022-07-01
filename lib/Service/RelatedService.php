<?php

declare(strict_types=1);

/**
 * Nextcloud - Related Resources
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2022
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


namespace OCA\RelatedResources\Service;


use OCA\Circles\CirclesManager;
use OCA\RelatedResources\Db\ItemRequest;
use OCA\RelatedResources\Exceptions\RelatedResourceProviderNotFound;
use OCA\RelatedResources\IRelatedResource;
use OCA\RelatedResources\IRelatedResourceProvider;
use OCA\RelatedResources\RelatedResourceProviders\DeckRelatedResourceProvider;
use OCA\RelatedResources\RelatedResourceProviders\FilesRelatedResourceProvider;
use OCA\RelatedResources\RelatedResourceProviders\TalkRelatedResourceProvider;

class RelatedService {

	private CirclesManager $circlesManager;


	public function __construct() {
		$this->circlesManager = \OC::$server->get(CirclesManager::class);
	}


	/**
	 * @param string $providerId
	 * @param string $itemId
	 *
	 * @return IRelatedResource[]
	 * @throws RelatedResourceProviderNotFound
	 */
	public function getRelatedToItem(string $providerId, string $itemId): array {
		$recipients = $this->getRelatedResourceProvider($providerId)
						   ->getSharesRecipients($itemId);

		$result = [];
		foreach ($this->getRelatedResourceProviders() as $provider) {
			$known = [];
			if ($providerId === $provider->getProviderId()) {
				$known[] = $itemId;
			}

			foreach ($provider->getRelatedToEntities($recipients) as $related) {
				if (in_array($related->getItemId(), $known)) {
					continue;
				}

				$result[] = $related;
				$known[] = $related->getItemId();
			}
		}

		return $result;
	}


	/**
	 * @return IRelatedResourceProvider[]
	 */
	private function getRelatedResourceProviders(): array {
		return [
			\OC::$server->get(FilesRelatedResourceProvider::class),
			\OC::$server->get(DeckRelatedResourceProvider::class),
			\OC::$server->get(TalkRelatedResourceProvider::class),
		];
	}

	/**
	 * @param string $relatedProviderId
	 *
	 * @return IRelatedResourceProvider
	 * @throws RelatedResourceProviderNotFound
	 */
	private function getRelatedResourceProvider(string $relatedProviderId): IRelatedResourceProvider {
		foreach ($this->getRelatedResourceProviders() as $provider) {
			if ($provider->getProviderId() === $relatedProviderId) {
				return $provider;
			}
		}

		throw new RelatedResourceProviderNotFound();
	}


}
