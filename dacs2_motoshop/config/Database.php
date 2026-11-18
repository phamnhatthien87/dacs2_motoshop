<?php

/**
 * Kết nối MySQL dùng chung cho các Model của MotoShop.
 * Cách dùng: $pdo = Database::getConnection();
 */
class Database
{
    private const HOST = 'localhost';
    private const PORT = 3306;
    private const NAME = 'motoshop';
    private const USERNAME = 'root';
    private const PASSWORD = ''; // Đổi nếu MySQL của bạn đã đặt mật khẩu.

    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        // Tái sử dụng kết nối trong cùng một request PHP.
        if (self::$connection !== null) {
            return self::$connection;
        }

        $dsn = 'mysql:host=' . self::HOST
            . ';port=' . self::PORT
            . ';dbname=' . self::NAME
            . ';charset=utf8mb4';

        try {
            self::$connection = new PDO($dsn, self::USERNAME, self::PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            // Chỉ ghi chi tiết vào log phía server, không đưa ra giao diện.
            error_log('MotoShop database connection failed: ' . $exception->getMessage());
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại sau.');
        }

        return self::$connection;
    }
}