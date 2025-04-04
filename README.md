## 설치 및 실행 방법

이 프로젝트는 PHP와 MySQL(또는 MariaDB)을 사용하여 구현되었으며, Docker를 사용하여 쉽게 설치하고 실행할 수 있습니다.

## 설치 및 실행 방법

### 1. 요구사항
- **Docker** 및 **Docker Compose**가 설치되어 있어야 합니다.
- **Git**이 설치되어 있어야 합니다.

### 2. 프로젝트 클론
터미널에서 다음 명령어를 실행하여 프로젝트를 클론합니다:

```bash
git clone https://github.com/klettermi/calendar.git
cd calendar
```

### 3.
docker-compose up --build


## 용법 메뉴얼

### 로그인 및 회원가입

- [http://localhost:8080/login.php](http://localhost:8080/login.php)에서 로그인합니다.
- 계정이 없으면 [http://localhost:8080/register.php](http://localhost:8080/register.php)에서 회원가입합니다.

### 일정 등록 및 관리

- 로그인 후, 대시보드([http://localhost:8080/dashboard.php](http://localhost:8080/dashboard.php))에서 자신의 일정 목록을 확인할 수 있습니다.
- "새 일정 추가" 버튼 또는 캘린더 페이지([http://localhost:8080/calendar.php](http://localhost:8080/calendar.php))에서 날짜 셀을 클릭하여 일정 등록 폼으로 이동합니다.
- 스케줄 등록 폼에서는 일정 종류, 제목, 장소, 시작 및 종료 시간, 참여자(체크박스 방식) 등을 입력합니다.
- 등록된 일정은 대시보드와 캘린더 모두에 표시됩니다.
- 일정 수정, 삭제, 복사 기능은 대시보드와 일정 상세 페이지에서 사용할 수 있습니다.

### 관리자 기능

- 관리자는 모든 사용자의 일정을 확인할 수 있습니다.
- 관리자 계정은 설치 시 기본적으로 생성되며, 추가로 사용자 관리 및 시스템 통계 페이지를 구현할 수 있습니다.
