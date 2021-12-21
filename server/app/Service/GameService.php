<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Service;

use App\Constants\GameCell;
use App\Constants\GameMessage;
use App\Constants\RoomMessage;
use App\Repository\Interfaces\GameInterface;
use App\Repository\Interfaces\RoomInterface;

/**
 * @property GameInterface $gameRepo
 * @property RoomInterface $roomRepo
 * @property MemberService $memberService
 */
class GameService extends Service
{
    /**
     * @var \App\Repository\Repository|\Hyperf\DbConnection\Model\Model|\Psr\Container\ContainerInterface|void
     */
    private $size = 15;

    public function go($uid, $sid, $data): bool
    {
        $room = $this->roomRepo->details($data['id']);
        if (! $game = $this->goPiece($uid, $data, $room)) {
            return false;
        }
        $return = [];
        $winner = $this->referee($data['x'], $data['y'], $game);
        if ($winner === GameCell::NONE) {
            $return['winner'] = null;
        } else {
            if ($winner === $game['player1_color']) {
                $winnerMemberId = $room['player1_id'];
            } elseif ($winner === $game['player2_color']) {
                $winnerMemberId = $room['player2_id'];
            } else {
                return false;
            }
            $this->deleteGame($room['id']);
            $return['winner']      = $this->memberService->getMember($winnerMemberId);
            $room['player1_ready'] = 0;
            $room['player2_ready'] = 0;
            $room['status']        = 1;
            $this->roomRepo->updateSingleData($data['id'], [
                'player1_ready' => 0,
                'player2_ready' => 0,
                'status'        => 1,
            ]);
        }
        $return['game'] = $game;
        defer(function () use ($room, $return) {
            socketIo()->of('/room')->to($room['id'])->emit(
                RoomMessage::ROOM_SHOW,
                success_reply('成功', $room)
            );

            socketIo()->of('/room')->to($room['id'])->emit(
                GameMessage::GAME_SHOW,
                success_reply('成功', $return)
            );
        });
        return true;
    }

    public function store($roomId): array
    {
        $game = [
            'room_id' => $roomId,
            'size'    => $this->size,
        ];
        if (random_int(0, 1) === 1) {
            $game['player1_color'] = GameCell::BLACK_PIECE;
            $game['player2_color'] = GameCell::WHITE_PIECE;
        } else {
            $game['player1_color'] = GameCell::WHITE_PIECE;
            $game['player2_color'] = GameCell::BLACK_PIECE;
        }
        $game['current_piece'] = GameCell::BLACK_PIECE;
        $game['map']           = $this->initMap();
        return $this->gameRepo->createSingleData($game);
    }

    public function deleteGame($roomId)
    {
        return $this->gameRepo->roomIdDelGame($roomId);
    }

    public function roomIdGetGameBy($roomId): array
    {
        return $this->gameRepo->roomIdGetGameBy($roomId);
    }

    public function clearGame()
    {
        $this->gameRepo->clearGame();
    }

    /**
     * 初始化棋盘.
     *
     * @return array
     */
    private function initMap(): array
    {
        $gameMap = [];
        for ($i = 0; $i < $this->size; ++$i) {
            for ($j = 0; $j < $this->size; ++$j) {
                $gameMap[$i][$j] = GameCell::NONE;
            }
        }
        return $gameMap;
    }

    /**
     * 设置棋盘.
     *
     * @param $game
     * @param int $x
     * @param int $y
     * @param int $value
     */
    private function setCell(int $x, int $y, int $value, &$game)
    {
        $game['map'][$x][$y] = $value;
        $game['last_go_x']   = $x;
        $game['last_go_y']   = $y;
    }

    /**
     * @param $uid
     * @param $data
     * @param $room
     *
     * @return array|false
     */
    private function goPiece($uid, $data, $room)
    {
        $game         = $this->roomIdGetGameBy($data['id']);
        $currentPiece = $game['current_piece'];
        if ($currentPiece === $game['player1_color'] && $uid !== $room['player1_id']) {
            return false;
        }
        if ($currentPiece === $game['player2_color'] && $uid !== $room['player2_id']) {
            return false;
        }
        $this->setCell($data['x'], $data['y'], $currentPiece, $game);
        if ($currentPiece === GameCell::BLACK_PIECE) {
            $game['current_piece'] = GameCell::WHITE_PIECE;
        } else {
            $game['current_piece'] = GameCell::BLACK_PIECE;
        }
        return $this->gameRepo->updateGo($game);
    }

    /**
     * 判断输赢，传入最后下子位置
     * 返回胜利方颜色.
     *
     * @param int $x
     * @param int $y
     * @param $game
     *
     * @return int
     */
    private function referee(int $x, int $y, $game): int
    {
        $color = $game['map'][$x][$y] ?? GameCell::NONE;
        if ($color === GameCell::NONE) {
            return GameCell::NONE;
        }
        $directionRules = [
            'leftRight'           => ['x' => 1, 'y' => 0],
            'upDown'              => ['x' => 0, 'y' => 1],
            'LeftUpperRightLower' => ['x' => -1, 'y' => -1],
            'RightUpperLowerLeft' => ['x' => 1, 'y' => -1],
        ];
        foreach ($directionRules as $directionRule) {
            $pieceCount = 1;
            $xStep      = $directionRule['x'];
            $yStep      = $directionRule['y'];
            foreach ([1, -1] as $num) {
                for ($i = 1; $i < 5; ++$i) {
                    $tmpX = $x + $xStep * $i * $num;
                    $tmpY = $y + $yStep * $i * $num;
                    if ($color === ($game['map'][$tmpX][$tmpY] ?? GameCell::NONE)) {
                        if (++$pieceCount >= 5) {
                            return $color;
                        }
                    } else {
                        break;
                    }
                }
            }
        }
        return GameCell::NONE;
    }
}
