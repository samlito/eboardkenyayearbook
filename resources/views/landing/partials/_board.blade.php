<section class="section section-alt" id="board">
    <div class="container">
        <div class="section-head reveal">
            <span class="section-kicker">Leadership</span>
            <h2 class="section-title">Board of Directors</h2>
            <p class="section-lead">{{ setting('institution_name', 'Our Institution') }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($members as $index => $member)
            <div class="col-sm-6 col-md-4 col-lg-3 reveal" style="transition-delay: {{ ($index % 4) * 0.1 }}s;">
                <div class="board-card">
                    <div class="board-avatar-wrap">
                        @if(!empty($member['photo_url']))
                            <img src="{{ asset($member['photo_url']) }}" alt="{{ $member['name'] ?? 'Member' }}" class="board-avatar" loading="lazy">
                        @else
                            <div class="board-avatar-placeholder" aria-hidden="true">
                                {{ strtoupper(substr($member['name'] ?? 'M', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h3>{{ $member['name'] ?? 'Member' }}</h3>
                    @if(!empty($member['role']))
                        <span class="board-role">{{ $member['role'] }}</span>
                    @endif
                    @if(!empty($member['designation']))
                        <span class="board-designation">{{ $member['designation'] }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
