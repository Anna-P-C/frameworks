<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SponsorController
{
    private function getPDO(): \PDO
    {
        return new \PDO("mysql:host=127.0.0.1;dbname=frameworks_db;charset=utf8", "root", "");
    }

    #[Route('/sponsors', name: 'get_sponsors', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $stmt = $this->getPDO()->query("SELECT id, name, amount FROM sponsors");
        return new JsonResponse($stmt->fetchAll(\PDO::FETCH_ASSOC), 200);
    }

    #[Route('/sponsors', name: 'create_sponsor', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['amount'])) {
            return new JsonResponse(['error' => 'Missing fields'], 400);
        }

        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("INSERT INTO sponsors (name, amount, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$data['name'], $data['amount']]);

        $id = $pdo->lastInsertId();
        return new JsonResponse(['id' => $id, 'name' => $data['name'], 'amount' => $data['amount']], 201);
    }

    #[Route('/sponsors/{id}', name: 'update_sponsor', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pdo = $this->getPDO();

        $stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
        $stmt->execute([$id]);
        $sponsor = $stmt->fetch();

        if (!$sponsor) {
            return new JsonResponse(['error' => 'Sponsor not found'], 404);
        }

        $name = $data['name'] ?? $sponsor['name'];
        $amount = $data['amount'] ?? $sponsor['amount'];

        $stmt = $pdo->prepare("UPDATE sponsors SET name = ?, amount = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$name, $amount, $id]);

        return new JsonResponse(['id' => $id, 'name' => $name, 'amount' => $amount], 200);
    }

    #[Route('/sponsors/{id}', name: 'delete_sponsor', methods: ['DELETE'])]
    public function destroy(int $id): JsonResponse
    {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("DELETE FROM sponsors WHERE id = ?");
        $stmt->execute([$id]);

        return new JsonResponse(['message' => 'Sponsor deleted successfully'], 200);
    }
}
