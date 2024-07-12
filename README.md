### Base wp traefik 2 (if not already set)

    git clone git@gitlab..fr:dsf/docker-dev-host.git
    git checkout feat-traefik-v2

You'll probably have to create the .env file juste create it with the informations of .env.dist.wsl ten

     make traefik-v2-wsl

### Gitlab routine before every merge request

    git fetch -p
	git checkout develop   (or default branch)
	git rebase origin/develop
	git checkout newBranch
	git rebase origin/develop
	git push

### (extremely) recommanded branch name and commits

eg branchname:

    feature/...

eg commit :

    feature(num of ticket): "comment"