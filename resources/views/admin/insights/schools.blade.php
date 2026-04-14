<x-app-layout>
    <x-slot name="header">
        {{-- Custom header styles --}}
        <style>
            .insights-header-title {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .insights-header-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: #0F6E56;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .insights-header-icon svg {
                width: 16px;
                height: 16px;
            }
            .insights-header-text h2 {
                font-size: 17px;
                font-weight: 500;
                color: #111827;
                letter-spacing: -0.02em;
                line-height: 1.2;
            }
            .insights-header-text p {
                font-size: 12px;
                color: #6B7280;
                margin-top: 1px;
            }
            .insights-header-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .insights-live-badge {
                font-size: 11px;
                font-weight: 500;
                padding: 4px 10px;
                border-radius: 20px;
                background: #D1FAE5;
                color: #065F46;
                letter-spacing: 0.02em;
            }
        </style>

        <div class="insights-header-inner">
            <div class="insights-header-title">
                <div class="insights-header-icon">
                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 12L5.5 8 8.5 10 12 5.5 14 7V14H2V12Z" fill="white" fill-opacity="0.9"/>
                        <path d="M2 12L5.5 8 8.5 10 12 5.5 14 7" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="insights-header-text">
                    <h2>{{ __('Insights') }}</h2>
                </div>
            </div>
            <span class="insights-live-badge">Live</span>
        </div>
    </x-slot>

    <style>
        /* ── Fonts ───────────────────────────────────────────── */
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&family=DM+Mono:wght@400;500&display=swap');

        .insights-root {
            font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
        }

        /* ── Filter card ─────────────────────────────────────── */
        .filter-card {
            background: #ffffff;
            border: 0.5px solid #E5E7EB;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-section-label {
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9CA3AF;
            margin-bottom: 1rem;
        }

        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        @media (max-width: 640px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 500;
            color: #6B7280;
        }

        .field-input {
            height: 38px;
            padding: 0 12px;
            border: 0.5px solid #D1D5DB;
            border-radius: 8px;
            background: #F9FAFB;
            color: #111827;
            font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
        }

        .field-input:focus {
            border-color: #1D9E75;
            box-shadow: 0 0 0 3px rgba(29, 158, 117, 0.12);
            background: #ffffff;
        }

        .btn-apply {
            height: 38px;
            padding: 0 20px;
            background: #0F6E56;
            color: #E1F5EE;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background 0.15s, transform 0.1s;
            white-space: nowrap;
            text-decoration: none;
        }

        .btn-apply:hover { background: #085041; }
        .btn-apply:active { transform: scale(0.98); }

        /* ── Section meta ────────────────────────────────────── */
        .section-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .section-heading {
            font-size: 13px;
            font-weight: 500;
            color: #111827;
        }

        .section-date {
            font-size: 12px;
            color: #9CA3AF;
            font-family: 'DM Mono', ui-monospace, monospace;
        }

        /* ── Stat cards ──────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }

        .stat-card {
            background: #ffffff;
            border: 0.5px solid #E5E7EB;
            border-radius: 12px;
            padding: 1.25rem 1.375rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            border-radius: 12px 12px 0 0;
        }

        .stat-card--slate::before  { background: #888780; }
        .stat-card--teal::before   { background: #1D9E75; }
        .stat-card--indigo::before { background: #7F77DD; }
        .stat-card--violet::before { background: #534AB7; }

        .stat-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .stat-icon svg { width: 14px; height: 14px; }

        .stat-icon--slate  { background: #F1EFE8; }
        .stat-icon--teal   { background: #E1F5EE; }
        .stat-icon--indigo { background: #EEEDFE; }
        .stat-icon--violet { background: #EEEDFE; }

        .stat-meta {
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 4px;
            font-weight: 400;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 300;
            letter-spacing: -0.04em;
            line-height: 1;
            font-family: 'DM Mono', ui-monospace, monospace;
            color: #111827;
        }

        .stat-value--teal   { color: #0F6E56; }
        .stat-value--indigo { color: #534AB7; }
        .stat-value--violet { color: #3C3489; }

        .stat-note {
            margin-top: 8px;
            font-size: 11px;
            color: #9CA3AF;
        }

        /* ── Coverage bar ────────────────────────────────────── */
        .coverage-wrap {
            background: #F9FAFB;
            border: 0.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 1.125rem 1.375rem;
            margin-bottom: 1.5rem;
        }

        .coverage-labels {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .coverage-pct {
            font-family: 'DM Mono', ui-monospace, monospace;
            font-weight: 500;
            color: #111827;
        }

        .coverage-track {
            height: 6px;
            background: #E5E7EB;
            border-radius: 999px;
            overflow: hidden;
        }

        .coverage-fill {
            height: 100%;
            border-radius: 999px;
            background: #1D9E75;
            transition: width 0.6s ease;
        }

        /* ── Empty state ─────────────────────────────────────── */
        .empty-state {
            background: #ffffff;
            border: 0.5px solid #E5E7EB;
            border-radius: 12px;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .empty-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #FEF3C7;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }

        .empty-title {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin-bottom: 4px;
        }

        .empty-sub {
            font-size: 13px;
            color: #6B7280;
        }
    </style>

    <div class="py-10 insights-root">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter card --}}
            <div class="filter-card">
                <p class="filter-section-label">Filters</p>
                <form method="GET" class="filter-row">
                    <div class="field-group">
                        <label class="field-label" for="date">Date</label>
                        <input
                            class="field-input"
                            id="date"
                            name="date"
                            type="date"
                            value="{{ $date }}"
                        />
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="school_id">School</label>
                        <select class="field-input" id="school_id" name="school_id">
                            <option value="">Select a school…</option>
                            @foreach($schools as $school)
                                <option
                                    value="{{ $school->id }}"
                                    {{ (int)($schoolId ?? 0) === $school->id ? 'selected' : '' }}
                                >
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn-apply">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                                <circle cx="5.5" cy="5.5" r="4" stroke="currentColor" stroke-width="1.4"/>
                                <path d="M8.5 8.5L11.5 11.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                            </svg>
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            {{-- Stats --}}
            @if($selectedSchool && $stats)
                @php
                    $total    = max($stats['total_students'], 1);
                    $paid     = $stats['students_paid'];
                    $coverage = round(($paid / $total) * 100, 1);
                    $coveragePct = min(100, ($paid / $total) * 100);
                @endphp

                <div class="section-meta">
                    <span class="section-heading">{{ $selectedSchool->name }}</span>
                    <span class="section-date">{{ $date }}</span>
                </div>

                <div class="stats-grid">

                    {{-- Total students --}}
                    <div class="stat-card stat-card--slate">
                        <div class="stat-icon stat-icon--slate">
                            <svg viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                <circle cx="7" cy="4.5" r="2.5" stroke="#5F5E5A" stroke-width="1.2"/>
                                <path d="M2 12c0-2.21 2.239-4 5-4s5 1.79 5 4" stroke="#5F5E5A" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="stat-meta">Total students</p>
                        <p class="stat-value">{{ number_format($stats['total_students']) }}</p>
                        <p class="stat-note">Enrolled this period</p>
                    </div>

                    {{-- Students paid --}}
                    <div class="stat-card stat-card--teal">
                        <div class="stat-icon stat-icon--teal">
                            <svg viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                <path d="M2.5 7.5l3 3 6-6" stroke="#1D9E75" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="stat-meta">Students paid for meal</p>
                        <p class="stat-value stat-value--teal">{{ number_format($stats['students_paid']) }}</p>
                        <p class="stat-note">Confirmed payments</p>
                    </div>

                    {{-- Amount received --}}
                    <div class="stat-card stat-card--indigo">
                        <div class="stat-icon stat-icon--indigo">
                            <svg viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                <rect x="2" y="3" width="10" height="8" rx="1.5" stroke="#7F77DD" stroke-width="1.2"/>
                                <path d="M5 7h4M7 5v4" stroke="#7F77DD" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="stat-meta">Amount received (GH₵)</p>
                        <p class="stat-value stat-value--indigo">{{ number_format($stats['amount_received'], 2) }}</p>
                        <p class="stat-note">School revenue</p>
                    </div>

                    {{-- 1% amount --}}
                    <div class="stat-card stat-card--violet">
                        <div class="stat-icon stat-icon--violet">
                            <svg viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                <path d="M7 2v10M4 5h5.5a1.5 1.5 0 010 3H4" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="stat-meta">1% amount (GH₵)</p>
                        <p class="stat-value stat-value--violet">{{ number_format($stats['one_percent'], 2) }}</p>
                        <p class="stat-note">Platform fee</p>
                    </div>

                </div>

                {{-- Coverage bar --}}
                <div class="coverage-wrap">
                    <div class="coverage-labels">
                        <span>Meal coverage rate</span>
                        <span class="coverage-pct">{{ $coverage }}%</span>
                    </div>
                    <div class="coverage-track">
                        <div class="coverage-fill" style="width: {{ $coveragePct }}%"></div>
                    </div>
                </div>

            @elseif($schoolId)

                {{-- Empty state --}}
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 6v4M10 14h.01" stroke="#B45309" stroke-width="1.5" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="7.5" stroke="#B45309" stroke-width="1.2"/>
                        </svg>
                    </div>
                    <p class="empty-title">No data found</p>
                    <p class="empty-sub">No records match the selected school and date. Try adjusting your filters.</p>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>