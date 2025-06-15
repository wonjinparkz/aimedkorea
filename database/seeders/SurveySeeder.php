<?php

namespace Database\Seeders;

use App\Models\Survey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 눈 노화 컨디션 셀프 테스트
        Survey::create([
            'title' => '눈 노화 컨디션 셀프 테스트',
            'description' => '디지털 기기 사용으로 인한 눈의 피로도와 노화 정도를 자가진단합니다.',
            'checklist_items' => [
                ['label' => '전혀 그렇지 않다', 'score' => 0],
                ['label' => '그렇지 않다', 'score' => 1],
                ['label' => '보통이다', 'score' => 2],
                ['label' => '그렇다', 'score' => 3],
                ['label' => '매우 그렇다', 'score' => 4],
            ],
            'questions' => [
                ['label' => '하루에 디지털 기기(컴퓨터, 스마트폰 등)를 4시간 이상 사용한다'],
                ['label' => '눈이 자주 피로하고 뻑뻑한 느낌이 든다'],
                ['label' => '가까운 곳에서 먼 곳으로 시선을 옮길 때 초점이 잘 맞지 않는다'],
                ['label' => '밝은 빛이나 화면을 볼 때 눈부심이 심하다'],
                ['label' => '글자나 사물이 흐릿하게 보이거나 겹쳐 보인다'],
                ['label' => '눈 주위가 무겁고 두통이 자주 발생한다'],
                ['label' => '눈을 자주 비비거나 깜빡이게 된다'],
                ['label' => '야간에 시력이 낮보다 현저히 떨어진다'],
                ['label' => '눈물이 자주 나거나 반대로 눈이 매우 건조하다'],
                ['label' => '휴식을 취해도 눈의 피로가 쉽게 회복되지 않는다'],
            ],
        ]);

        // 뇌신경 노화 셀프 테스트
        Survey::create([
            'title' => '뇌신경 노화 셀프 테스트',
            'description' => '디지털 과부하로 인한 뇌신경계의 피로도와 기능 저하를 평가합니다.',
            'checklist_items' => [
                ['label' => '전혀 그렇지 않다', 'score' => 0],
                ['label' => '그렇지 않다', 'score' => 1],
                ['label' => '보통이다', 'score' => 2],
                ['label' => '그렇다', 'score' => 3],
                ['label' => '매우 그렇다', 'score' => 4],
            ],
            'questions' => [
                ['label' => '최근 기억력이 예전보다 떨어진 것 같다'],
                ['label' => '집중력이 저하되어 한 가지 일에 오래 집중하기 어렵다'],
                ['label' => '멀티태스킹을 하면 쉽게 혼란스럽고 실수가 잦다'],
                ['label' => '단순한 계산이나 판단이 예전보다 어렵게 느껴진다'],
                ['label' => '대화 중 적절한 단어가 떠오르지 않을 때가 많다'],
                ['label' => '새로운 정보를 학습하는 속도가 느려졌다'],
                ['label' => '피로감이 지속되고 정신적으로 무기력함을 느낀다'],
                ['label' => '감정 조절이 어렵고 스트레스에 민감해졌다'],
                ['label' => '수면의 질이 떨어지고 숙면을 취하기 어렵다'],
                ['label' => '일상적인 업무나 활동에서 동기부여가 떨어진다'],
            ],
        ]);

        // 디지털 수면 패턴 자가분석
        Survey::create([
            'title' => '디지털 수면 패턴 자가분석',
            'description' => '디지털 기기 사용이 수면에 미치는 영향을 분석하고 수면의 질을 평가합니다.',
            'checklist_items' => [
                ['label' => '전혀 그렇지 않다', 'score' => 0],
                ['label' => '그렇지 않다', 'score' => 1],
                ['label' => '보통이다', 'score' => 2],
                ['label' => '그렇다', 'score' => 3],
                ['label' => '매우 그렇다', 'score' => 4],
            ],
            'questions' => [
                ['label' => '잠들기 전 1시간 이내에 스마트폰이나 태블릿을 사용한다'],
                ['label' => '침대에서 디지털 기기를 사용하는 습관이 있다'],
                ['label' => '잠들기까지 30분 이상 걸리는 경우가 많다'],
                ['label' => '수면 중 자주 깨고 다시 잠들기 어렵다'],
                ['label' => '아침에 일어나도 피로가 풀리지 않은 느낌이다'],
                ['label' => '낮 시간에 졸음이나 피로감을 자주 느낀다'],
                ['label' => '수면 시간이 불규칙하고 일정하지 않다'],
                ['label' => '잠들기 전 SNS나 동영상 시청에 많은 시간을 보낸다'],
                ['label' => '밤에 스마트폰 알림 소리 때문에 잠에서 깬 적이 있다'],
                ['label' => '주말에 평일보다 2시간 이상 늦게 일어난다'],
            ],
        ]);
    }
}
