A. 코드·릴리스

    Git PR 머지 완료 + 커밋 해시 기록
    Git 태그/릴리스 노트: v1.0-stage1 (변경내역·영향 범위·롤백 방법 포함)
    웹/관리자 빌드 아티팩트(ZIP 또는 Docker 이미지) 전달

B. DB·마이그레이션

    ERD & 마이그레이션 스크립트(버전명시), 샘플/시드 데이터
    배포 전/후 DB 스냅샷(덤프) — 최소 1회분

C. 설정·운영

    .env.sample 최신화(1단계에서 추가/변경된 키 주석 포함)
    런북 1p: 백업·롤백·장애 대응(연락 채널/평시/야간)
    접근 권한 표: Admin·Editor 등 최종 RBAC 매트릭스

D. 증빙(Proof Pack)

    사이드바/북마크 동작 영상(30초)
    RBAC 403 스크린샷(권한 낮춘 계정으로 직접 접근)
    CSV Export 샘플 파일(필터 반영 확인)
    Lighthouse/웹바이탈 리포트 PDF
    GA4 디버그뷰 캡처(menu_click/section_view/route_guard_block 3건 이상 수집)

E. 소유권·권한 정리

    리포지토리 소유자=AI-Med 조직(개발사는 Maintainer)
    클라우드/IAM: 우리 계정 소유, 개발사 Admin(Owner 금지)
    키 교체: 기존 임시 키 회수, 신규 키로 재발급(24시간 내)
