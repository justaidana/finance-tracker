@extends('layouts.app')
@section('title', __('app.advice'))
@section('page-title', __('app.advice'))

@section('content')
@php
$sections = app()->getLocale() === 'ru' ? [
    [
        'icon' => 'chart-bar',
        'title' => 'Правило 50/30/20',
        'body' => [
            'Это один из наиболее простых и эффективных способов управления личным бюджетом. Разделите свой доход после уплаты налогов на три части: 50% — на необходимые расходы (аренда, еда, транспорт), 30% — на желания (развлечения, рестораны), 20% — на сбережения и погашение долгов.',
            'Это правило помогает сохранять баланс между жизненными потребностями, личным удовольствием и финансовой безопасностью. Если вы тратите на нужды больше 50%, ищите способы сократить фиксированные расходы.',
            'Начните с отслеживания текущих расходов в течение одного месяца — вы удивитесь, сколько уходит на ненужное.',
        ],
        'cta' => 'Настроить бюджет →',
    ],
    [
        'icon' => 'piggy',
        'title' => 'Как создать финансовую подушку',
        'body' => [
            'Финансовая подушка безопасности — это сумма, которую вы откладываете на случай непредвиденных обстоятельств: потеря работы, болезнь, срочный ремонт. Рекомендуется иметь сумму, покрывающую от 3 до 6 месяцев ваших расходов.',
            'Храните подушку на отдельном сберегательном счёте, чтобы не тратить её на повседневные нужды. Не инвестируйте эти деньги в высокорисковые активы — они должны быть доступны в любой момент.',
            'Начните с небольших шагов: откладывайте 5–10% дохода каждый месяц, пока не накопите нужную сумму.',
        ],
        'cta' => 'Создать цель накопления →',
    ],
    [
        'icon' => 'trending',
        'title' => 'Основы инвестирования',
        'body' => [
            'Сложный процент — это начисление процентов не только на основную сумму, но и на ранее начисленные проценты. Это означает, что чем раньше вы начинаете инвестировать, тем больше ваш капитал растёт со временем.',
            'ETF (биржевые фонды) — это удобный инструмент для начинающих инвесторов. Они позволяют купить сразу множество акций или облигаций, снижая риски через диверсификацию при минимальных комиссиях.',
            'Правило: инвестируйте только те деньги, которые не понадобятся вам в ближайшие 3–5 лет. Никогда не инвестируйте на заёмные средства.',
        ],
        'cta' => 'Начать откладывать →',
    ],
    [
        'icon' => 'receipt',
        'title' => 'Как выбраться из долгов',
        'body' => [
            'Метод «лавины»: сначала погашайте долги с наибольшей процентной ставкой, сохраняя минимальные платежи по остальным. Это математически выгоднее — вы платите меньше процентов в долгосрочной перспективе.',
            'Метод «снежного кома»: закрывайте сначала наименьшие по размеру долги. Это даёт психологическое удовлетворение и мотивацию продолжать.',
            'Главное правило: не берите новые долги, пока не расплатились со старыми. Автоматизируйте платежи, чтобы не пропускать сроки и не накапливать штрафы.',
        ],
        'cta' => 'Отслеживать расходы →',
    ],
    [
        'icon' => 'check',
        'title' => 'Привычки умного сбережения',
        'body' => [
            'Платите себе первому: как только получили зарплату — сразу переведите запланированную сумму на сберегательный счёт, прежде чем начать тратить. Это снимает необходимость «находить» деньги в конце месяца.',
            'Избегайте инфляции образа жизни. Каждый раз, когда вы получаете прибавку к зарплате, попробуйте сохранить 50% этой прибавки, а не тратить всё на новые расходы.',
            'Используйте правило 24 часов: прежде чем сделать незапланированную покупку на сумму выше 5 000 ₸, подождите сутки. Часто желание проходит само.',
        ],
        'cta' => 'Открыть бюджет →',
    ],
    [
        'icon' => 'book',
        'title' => 'Как ставить и достигать финансовые цели',
        'body' => [
            'Ставьте конкретные, измеримые цели с чёткими сроками. «Хочу накопить 500 000 ₸ на отпуск через 12 месяцев» работает гораздо лучше, чем «хочу больше откладывать».',
            'Разделите большую цель на ежемесячные сбережения. Если цель — 500 000 ₸ за год, вам нужно откладывать около 42 000 ₸ в месяц. Это конкретная, управляемая задача.',
            'Визуализируйте прогресс. Когда вы видите, как ваш прогресс-бар заполняется, это мотивирует продолжать. Не трогайте сбережения на цель ни при каких обстоятельствах.',
        ],
        'cta' => 'Создать цель →',
    ],
] : [
    [
        'icon' => 'chart-bar',
        'title' => 'The 50/30/20 Budgeting Rule',
        'body' => [
            'One of the simplest and most effective personal budgeting frameworks. Divide your after-tax income into three buckets: 50% for needs (rent, food, transport), 30% for wants (entertainment, dining out), and 20% for savings and debt repayment.',
            'This rule helps maintain balance between life necessities, personal enjoyment, and financial security. If your needs exceed 50%, look for ways to reduce fixed costs like rent or subscriptions.',
            'Start by tracking your current spending for one month — you may be surprised how much goes to things you don\'t really value.',
        ],
        'cta' => 'Set up your budget →',
    ],
    [
        'icon' => 'piggy',
        'title' => 'Building an Emergency Fund',
        'body' => [
            'An emergency fund is money set aside for unexpected events: job loss, medical bills, urgent repairs. Aim for 3–6 months of living expenses — this gives you a financial buffer without needing to go into debt.',
            'Keep it in a separate savings account so you\'re not tempted to spend it day-to-day. Don\'t invest it in volatile assets — it must be accessible instantly when you need it.',
            'Start small: save 5–10% of your income each month until you reach your target. Even $500 in savings dramatically reduces financial stress.',
        ],
        'cta' => 'Create a savings goal →',
    ],
    [
        'icon' => 'trending',
        'title' => 'Basics of Investing',
        'body' => [
            'Compound interest means earning interest on both your principal and previously earned interest. This creates exponential growth — the earlier you start, the more powerful the effect over decades.',
            'ETFs (Exchange-Traded Funds) are ideal for beginners. They let you instantly own hundreds of stocks or bonds, providing instant diversification at a low cost. Index ETFs typically outperform actively managed funds over 10+ years.',
            'Rule: only invest money you won\'t need for at least 3–5 years. Never invest borrowed money. Consistency matters more than timing — regular monthly contributions beat trying to time the market.',
        ],
        'cta' => 'Start saving →',
    ],
    [
        'icon' => 'receipt',
        'title' => 'Getting Out of Debt',
        'body' => [
            'The Debt Avalanche: pay minimum on all debts, then throw all extra money at the highest-interest debt first. Once it\'s gone, attack the next highest. This minimizes total interest paid — the mathematically optimal approach.',
            'The Debt Snowball: pay off the smallest balance first regardless of interest rate. The quick wins build momentum and motivation, which often leads to better long-term success.',
            'The golden rule: stop adding new debt while paying off old ones. Automate minimum payments so you never miss a due date and accumulate late fees.',
        ],
        'cta' => 'Track your spending →',
    ],
    [
        'icon' => 'check',
        'title' => 'Smart Saving Habits',
        'body' => [
            'Pay yourself first: the moment you receive your paycheck, immediately move your savings target to a separate account — before spending anything. This removes the need to "find" savings at month end.',
            'Avoid lifestyle inflation. Each time you get a raise, save at least 50% of the increase rather than expanding your lifestyle to match. Your future self will thank you.',
            'Apply the 24-hour rule for unplanned purchases over $50. Wait a day before buying — the urge often passes on its own, saving you money on things you didn\'t truly need.',
        ],
        'cta' => 'Open budget planner →',
    ],
    [
        'icon' => 'book',
        'title' => 'Setting and Reaching Financial Goals',
        'body' => [
            'Set specific, measurable goals with clear deadlines. "Save $5,000 for a vacation in 12 months" is far more powerful than "save more money." Vague goals produce vague results.',
            'Break big goals into monthly savings targets. $5,000 in 12 months = roughly $417/month. That\'s a concrete, manageable task you can plug directly into your budget.',
            'Visualize your progress. Watching a progress bar fill toward your goal is motivating. Treat goal savings as completely off-limits — as untouchable as a bill you must pay.',
        ],
        'cta' => 'Create a goal →',
    ],
];
@endphp

<div class="space-y-4">
    @foreach($sections as $i => $section)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                @include('components.icon', ['name' => $section['icon'], 'class' => 'w-5 h-5 text-green-600'])
            </div>
            <div class="flex-1">
                <h2 class="text-base font-semibold text-gray-900 mb-3">{{ $section['title'] }}</h2>
                @foreach($section['body'] as $para)
                    <p class="text-sm text-gray-600 leading-relaxed mb-2">{{ $para }}</p>
                @endforeach
                <a href="{{ route('budgets.index') }}"
                   class="inline-flex items-center mt-3 text-sm font-medium text-green-600 hover:text-green-700 hover:underline">
                    {{ $section['cta'] }}
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
