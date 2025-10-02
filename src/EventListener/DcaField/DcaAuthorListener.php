<?php

namespace HeimrichHannot\UtilsBundle\EventListener\DcaField;

use Contao\BackendUser;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\DataContainer;
use Contao\FrontendUser;
use Contao\Model;
use HeimrichHannot\UtilsBundle\Dca\AuthorField;
use HeimrichHannot\UtilsBundle\Dca\AuthorFieldConfiguration;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DcaAuthorListener extends AbstractDcaFieldListener
{
    #[AsHook("loadDataContainer")]
    public function onLoadDataContainer(string $table): void
    {
        if (!isset(AuthorField::getRegistrations()[$table])) {
            return;
        }

        $options = AuthorField::getRegistrations()[$table];
        $authorFieldName = $this->getAuthorFieldName($options);
        $authorField = $this->createAuthorField($options);

        $GLOBALS['TL_DCA'][$table]['fields'][$authorFieldName] = $authorField;
        $GLOBALS['TL_DCA'][$table]['config']['oncreate_callback'][] = [self::class, 'onConfigCreateCallback'];
        $GLOBALS['TL_DCA'][$table]['config']['oncopy_callback'][] = [self::class, 'onConfigCopyCallback'];
    }

    public function onConfigCreateCallback(string $table, int $id, array $row, DataContainer $dc): void
    {
        $option = AuthorField::getRegistrations()[$dc->table] ?? null;
        $model = $this->getModelInstance($table, $id);
        if (!$model || !$option) {
            return;
        }

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->container->get('token_storage');
        $user = $tokenStorage->getToken()?->getUser();
        if (!$user) {
            return;
        }

        $this->setAuthor($option, $user, $model);
        $model->save();
    }
    public function onConfigCopyCallback(int $insertId, DataContainer $dc): void
    {
        $options = AuthorField::getRegistrations()[$dc->table];
        $model = $this->getModelInstance($dc->table, $insertId);
        if (!$model || !$options) {
            return;
        }

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->container->get('token_storage');
        $user = $tokenStorage->getToken()?->getUser();

        $this->setAuthor($options, $user, $model);
        $model->save();
    }

    /**
     * @param AuthorFieldConfiguration $options
     * @return string
     */
    protected function getAuthorFieldName(AuthorFieldConfiguration $options): string
    {
        if (!$options->hasFieldNamePrefix()) {
            return 'author';
        }
        if (str_ends_with($options->getFieldNamePrefix(), '_')) {
            return $options->getFieldNamePrefix() . 'author';
        } else {
            return $options->getFieldNamePrefix() . 'Author';
        }
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services['token_storage'] = TokenStorageInterface::class;
        return $services;
    }

    public function createAuthorField(AuthorFieldConfiguration $options): array
    {
        $authorField = [
            'inputType' => 'select',
            'eval' => [
                'doNotCopy' => true,
                'mandatory' => true,
                'chosen' => true,
                'includeBlankOption' => true,
                'tl_class' => 'w50'
            ],
            'sql' => "int(10) unsigned NOT NULL default 0",
        ];

        $this->applyDefaultFieldAdjustments($authorField, $options);

        if ($options->isUseDefaultLabel()) {
            $authorField['label'] = &$GLOBALS['TL_LANG']['MSC']['utilsBundle']['author'];
        }

        $authorField['default'] = 0;
        if (AuthorField::TYPE_USER === $options->getType()) {
            $authorField['foreignKey'] = 'tl_user.name';
            $authorField['relation'] = ['type' => 'hasOne', 'load' => 'lazy'];
        } elseif (AuthorField::TYPE_MEMBER === $options->getType()) {
            $authorField['foreignKey'] = "tl_member.CONCAT(firstname,' ',lastname)";
            $authorField['relation'] = ['type' => 'hasOne', 'load' => 'lazy'];
        }
        return $authorField;
    }

    private function setAuthor(AuthorFieldConfiguration $options, ?UserInterface $user, Model $model): void
    {
        $authorFieldName = $this->getAuthorFieldName($options);
        $model->{$authorFieldName} = 0;
        if (AuthorField::TYPE_USER === $options->getType()) {
            if ($user instanceof BackendUser) {
                $model->{$authorFieldName} = $user->id;
            }
        } elseif (AuthorField::TYPE_MEMBER === $options->getType()) {
            if ($user instanceof FrontendUser) {
                $model->{$authorFieldName} = $user->id;
            }
        }
    }
}