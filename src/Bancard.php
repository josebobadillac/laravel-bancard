<?php 

namespace Mancoide\Bancard;

use Mancoide\Bancard\Operations\{
    PreAuthorizationConfirm,
    SingleBuy,
    NewCard,
    ListCards,
    DeleteCard,
    SingleBuyZimple,
    TokenCharge,
    Confirmation,
    Rollback,
    WebhookConfirm
};
use Illuminate\Http\Client\Response;

class Bancard
{
    public static string $production = 'https://vpos.infonet.com.py';
    public static string $staging = 'https://vpos.infonet.com.py:8888';
    public static string $script = 'checkout/javascript/dist/bancard-checkout-3.0.0.js';

    public static function isStaging(): bool
    {
        return config('bancard.staging');
    }

    public static function baseUrl(): string
    {
        return self::isStaging() ? self::$staging : self::$production;
    }

    public static function publicKey(): string
    {
        return config('bancard.public');
    }

    public static function privateKey(): string
    {
        return config('bancard.private');
    }

    public static function scriptUrl(): string
    {
        $baseUrl = self::baseUrl();
        $script = self::$script;

        return "{$baseUrl}/{$script}";
    }

    /**
     * Start the payment process.
     *
     * @param string $description Description of the payment.
     * @param float $amount Amount in Guaraníes.
     * @param string|null $process_id (Optional) Order ID.
     * @param bool $pre_authorize (Optional) Indicates if the order must be pre-authorized.
     * @return Response
     */
    public static function singleBuy(string $description, float $amount, string $process_id = null, bool $pre_authorize = false): Response
    {
        $operation = new SingleBuy($description, $amount, $process_id, $pre_authorize);
        return $operation->makeRequest();
    }

    /**
     * Start the payment process with zimple wallet.
     *
     * @param string $description Description of the payment.
     * @param float $amount Amount in Guaraníes.
     * @param string $phone_number Client phone number
     * @param string|null $process_id Order ID
     * @return Response
     */
    public static function singleBuyZimple(string $description, float $amount, string $phone_number, string $process_id = null): Response
    {
        $operation = new SingleBuyZimple($description, $amount, $phone_number, $process_id);
        return $operation->makeRequest();
    }

    /**
     * Start de registration process of a card.
     *
     * @param integer $userId
     * @param string $userCellPhone
     * @param string $userMail
     * @return Response
     */
    public static function newCard(int $userId, string $userCellPhone, string $userMail): Response
    {
        $operation = new NewCard($userId, $userCellPhone, $userMail);
        return $operation->makeRequest();
    }

    /**
     * Operation that allows to list the cards registered by a user.
     *
     * @param integer $userId
     * @return Response
     */
    public static function listCards(int $userId): Response
    {
        $operation = new ListCards($userId);
        return $operation->makeRequest();
    }

    /**
     * Operation that allows you to delete a registered card.
     *
     * @param integer $userId
     * @param string $aliasToken
     * @return Response
     */
    public static function deleteCard(int $userId, string $aliasToken): Response
    {
        $operation = new DeleteCard($userId, $aliasToken);
        return $operation->makeRequest();
    }

    /**
     * Operation that allows payment with a token.
     *
     * @param string $description
     * @param float $amount
     * @param string $aliasToken
     * @param string|null $process_id (Optional) Order ID
     * @param bool $pre_authorize (Optional) Indicates if the order must be pre-authorized.
     * @return Response
     */
    public static function tokenCharge(string $description, float $amount, string $aliasToken, string $process_id = null, bool $pre_authorize = false): Response
    {
        $operation = new TokenCharge($description, $amount, $aliasToken, $process_id, $pre_authorize);
        return $operation->makeRequest();
    }

    /**
     * Operation that allow to know if a payment (occasional or with token) was confirmed or not.
     *
     * @param string $shopProcessId
     * @return Response
     */
    public static function confirmation(string $shopProcessId): Response
    {
        $operation = new Confirmation($shopProcessId);
        return $operation->makeRequest();
    }

    /**
     * Pre-Authorization Operation that confirm a pre-authorized payment
     *
     * @param string $shopProcessId
     * @param float $amount (Optional) Amount to be confirmed, cannot be higher than the pre-authorized amount
     * @return Response
     */
    public static function preAuthorizationConfirm(string $shopProcessId, float $amount = 0): Response
    {
        $operation = new PreAuthorizationConfirm($shopProcessId, $amount);
        return $operation->makeRequest();
    }

    /**
     * Operation that allows you to cancel the payment (occasional or with token).
     *
     * @param string $shopProcessId
     * @return Response
     */
    public static function rollback(string $shopProcessId): Response
    {
        $operation = new Rollback($shopProcessId);
        return $operation->makeRequest();
    }

    /**
     * Operation that parses the request sent by Bancard through the webhook
     *
     * @return array|null
     */
    public static function webhookConfirm(): ?array
    {
        $operation = new WebhookConfirm();
        return $operation->parseRequest();
    }
}
