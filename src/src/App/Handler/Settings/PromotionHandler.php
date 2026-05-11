<?php

declare(strict_types=1);

namespace App\Handler\Settings;

use App\Database\Database;
use App\Helper\Permission;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PromotionHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        if (!Session::get('user_id') || !Permission::isAllowed('admin')) {
            return new RedirectResponse('/dashboard');
        }

        $pdo      = $this->db->getPdo();
        $userName = Session::get('user_name');
        $role     = Session::get('user_role');

        if ($request->getMethod() === 'POST') {
            $body    = $request->getParsedBody();
            $enabled = isset($body['new_user_discount_enabled']) ? 1 : 0;
            $percent = min(100, max(0, (int)($body['new_user_discount_percent'] ?? 0)));

            $pdo->prepare(
                'UPDATE promotion_settings
                    SET new_user_discount_enabled = :enabled,
                        new_user_discount_percent = :percent
                  LIMIT 1'
            )->execute([':enabled' => $enabled, ':percent' => $percent]);

            Session::flash('promo_success', 'Promotion settings saved.');
            return new RedirectResponse('/settings/promotion');
        }

        $promo = $pdo->query(
            'SELECT new_user_discount_enabled, new_user_discount_percent
               FROM promotion_settings LIMIT 1'
        )->fetch();

        $content = Template::render('settings/promotion', [
            'promo'   => $promo ?: ['new_user_discount_enabled' => 0, 'new_user_discount_percent' => 0],
            'success' => Session::getFlash('promo_success'),
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content'      => $content,
                'userName'     => $userName,
                'role'         => $role,
                'currentRoute' => 'settings',
            ])
        );
    }
}
