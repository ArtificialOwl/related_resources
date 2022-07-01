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


namespace OCA\RelatedResources\Model;


use JsonSerializable;
use OCA\Circles\Model\FederatedUser;
use OCA\RelatedResources\Tools\Db\IQueryRow;
use OCA\RelatedResources\Tools\Traits\TArrayTools;


class FilesShare implements IQueryRow, JsonSerializable {
	use TArrayTools;


	private string $sharedWith = '';
	private int $shareType = 0;
	private ?FederatedUser $entity = null;
	private int $fileId = 0;
	private string $fileTarget = '';


	public function __construct() {
	}


	/**
	 * @param string $sharedWith
	 *
	 * @return FilesShare
	 */
	public function setSharedWith(string $sharedWith): self {
		$this->sharedWith = $sharedWith;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSharedWith(): string {
		return $this->sharedWith;
	}


	/**
	 * @param int $shareType
	 *
	 * @return FilesShare
	 */
	public function setShareType(int $shareType): self {
		$this->shareType = $shareType;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getShareType(): int {
		return $this->shareType;
	}


	/**
	 * @param int $fileId
	 *
	 * @return FilesShare
	 */
	public function setFileId(int $fileId): self {
		$this->fileId = $fileId;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getFileId(): int {
		return $this->fileId;
	}


	/**
	 * @param FederatedUser $entity
	 *
	 * @return FilesShare
	 */
	public function setEntity(FederatedUser $entity): self {
		$this->entity = $entity;

		return $this;
	}

	/**
	 * @return FederatedUser
	 */
	public function getEntity(): ?FederatedUser {
		return $this->entity;
	}


	/**
	 * @param string $fileTarget
	 *
	 * @return FilesShare
	 */
	public function setFileTarget(string $fileTarget): self {
		$this->fileTarget = $fileTarget;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileTarget(): string {
		return $this->fileTarget;
	}


	/**
	 * @param array $data
	 *
	 * @return IQueryRow
	 */
	public function importFromDatabase(array $data): IQueryRow {
		$this->setShareType($this->getInt('share_type', $data))
			 ->setSharedWith($this->get('share_with', $data))
			 ->setFileId($this->getInt('file_source', $data))
			 ->setFileTarget($this->get('file_target', $data));

		return $this;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'shareType' => $this->getShareType(),
			'sharedWith' => $this->getSharedWith(),
			'fileId' => $this->getFileId(),
			'fileTarget' => $this->getFileTarget(),
			'entity' => $this->getEntity()
		];
	}

}