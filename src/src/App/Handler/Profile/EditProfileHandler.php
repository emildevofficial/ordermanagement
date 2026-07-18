<?php

declare(strict_types=1);

namespace App\Handler\Profile;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EditProfileHandler implements RequestHandlerInterface
{
    public function __construct(private Database $db)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId = (int) (Session::get('user_id') ?? 0);

        if ($userId <= 0) {
            return new RedirectResponse('/login');
        }

        if ($request->getMethod() === 'POST') {
            $body = $request->getParsedBody();
            $name = trim((string) ($body['name'] ?? ''));

            if ($name === '') {
                return $this->renderForm('Name is required.');
            }

            $stmt = $this->db->getPdo()->prepare(
                'UPDATE users
                    SET name = :name,
                        updated_at = :updated_at
                  WHERE id = :id'
            );
            $stmt->execute([
                ':name' => $name,
                ':updated_at' => DateTimeHelper::nowForStorage(),
                ':id' => $userId,
            ]);

            Session::set('user_name', $name);
            Session::flash('profile_success', 'Profile updated successfully.');

            return new RedirectResponse('/profile');
        }

        return $this->renderForm();
    }

    private function renderForm(?string $error = null): HtmlResponse
    {
        $safeUserName = htmlspecialchars((string) (Session::get('user_name') ?? ''), ENT_QUOTES, 'UTF-8');
        $errorHtml = '';

        if ($error !== null) {
            $safeError = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
            $errorHtml = "
                <div class='mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700'>
                    {$safeError}
                </div>";
        }

        return new HtmlResponse("
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Edit Profile</title>
    <script src='https://cdn.tailwindcss.com/3.4.16'></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
</head>
<body class='bg-gray-100 flex items-center justify-center min-h-screen p-4'>
    <form method='POST' class='bg-white p-6 rounded-xl shadow w-full max-w-md'>
        <h2 class='text-lg font-bold mb-4'>Edit Profile</h2>
        {$errorHtml}

        <label class='block mb-2 text-sm'>Name</label>
        <input type='text' name='name' value='{$safeUserName}' required
            class='w-full border rounded px-3 py-2 mb-4' />

        <button type='submit'
            class='w-full bg-indigo-600 text-white py-2 rounded'>
            Save
        </button>

        <a href='/profile'
            class='block text-center mt-3 text-gray-500'>
            Cancel
        </a>
    </form>
</body>
</html>
");
    }
}
