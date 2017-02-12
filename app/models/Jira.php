<?php
class Jira extends AppModel
{


    // Gets one week of worklogs for the employee, starting from the import date
    public static function getFullWorklogsForEmployee($intEmployeeId, $dateImportFrom) {
    	return DB::connection('jira')->select(DB::raw(
		"select Project.ID as ProjectID, Project.pname, ifnull(Worklog.timeworked/3600, 0) as hours, DATE(Worklog.STARTDATE) as datestamp, Worklog.worklogbody, Issue.pkey, Issue.summary
            FROM worklog as Worklog
            INNER JOIN cwd_user as User on User.user_name = Worklog.AUTHOR
            INNER JOIN jiraissue as Issue on Issue.ID = Worklog.issueid
            INNER JOIN project as Project on Project.ID = Issue.PROJECT
            WHERE User.id = :employeeId and STARTDATE >= :dateImportFrom and STARTDATE < DATE_ADD(:dateImportFrom2, INTERVAL 7 DAY)
            ORDER BY Issue.PROJECT, datestamp"), array('employeeId'=>$intEmployeeId, 'dateImportFrom'=>$dateImportFrom, 'dateImportFrom2'=>$dateImportFrom));
    }

    // Gets one week of worklogs for the employee, starting from the import date
    // NOTE: In the query, you will see floor(hours * 4) / 4 ... This is to round hours to the nearest 15 minutes, so when someone
    // logs 1.348954359 hours, it shows up as 1.25 hours.

    public static function getWorklogsForEmployee($intEmployeeId, $dateImportFrom) {
        return DB::connection('jira')->select(DB::raw(
        "select Project.ID as ProjectID, Project.pname, ifnull(floor((sum(Worklog.timeworked)/3600)*4)/4, 0) as hours, DATE(Worklog.STARTDATE) as datestamp
            FROM worklog as Worklog
            INNER JOIN cwd_user as User on User.user_name = Worklog.AUTHOR
            INNER JOIN jiraissue as Issue on Issue.ID = Worklog.issueid
            INNER JOIN project as Project on Project.ID = Issue.PROJECT
            WHERE User.id = :employeeId and STARTDATE >= :dateImportFrom and STARTDATE < DATE_ADD(:dateImportFrom2, INTERVAL 7 DAY)
            GROUP BY Issue.PROJECT, datestamp"), array('employeeId'=>$intEmployeeId, 'dateImportFrom'=>$dateImportFrom, 'dateImportFrom2'=>$dateImportFrom));
    }

    public static function getOverageHours($projectId) {
      $project = \Project::find($projectId);
      if ($project == null) throw new Exception("Unable to find project ".$projectId);
      return DB::connection('jira')->select(DB::raw(
"
select User.id as user_id, DATE(w.STARTDATE) as datestamp, w.author, i.pkey, w.timeworked, ifnull(floor((w.timeworked/3600)*4)/4, 0) as hours, i.TIMESPENT, i.TIMEORIGINALESTIMATE, ifnull(floor((i.TIMEORIGINALESTIMATE/3600)*4)/4, 0) as estHours, it.pname, i.summary 
from worklog w
inner join jiraissue i on i.id = w.issueid
inner join issuetype it on it.id = i.issuetype 
INNER JOIN cwd_user as User on User.user_name = w.AUTHOR
where i.project = :projectExtId
AND i.TIMEORIGINALESTIMATE > 0
AND (
    (it.pname = 'Bug') 
    OR (it.pname != 'Bug' && i.TIMESPENT > i.TIMEORIGINALESTIMATE)
    )
ORDER BY i.pkey
"), array('projectExtId'=>$project->external_id));      
    }


    public static function getEmployees() {
        return DB::connection('jira')->select(DB::raw("select User.* from cwd_user User where email_address like '%ngdcorp.com'"));
    }

    public static function getProjectCategories() {
        return DB::connection('jira')->select(DB::raw("select ProjectCategory.* from projectcategory order by cname"));
        }

    public static function getProjectData() {
        return DB::connection('jira')->select(DB::raw("
            select Project.*, ProjectCategory.*, Project.ID as projectID, ProjectCategory.ID as companyID from project Project
            inner join nodeassociation na on na.source_node_id = Project.id and na.sink_node_entity = 'PermissionScheme'
            left join nodeassociation nacat on nacat.source_node_id = Project.id and nacat.sink_node_entity = 'ProjectCategory'
            left join projectcategory ProjectCategory on ProjectCategory.id = nacat.sink_node_id
            inner join permissionscheme ps on ps.id = na.sink_node_id
            where ps.name != 'Archived Projects'
            order by Project.pname"));
    }

    public static function getProjectList() {
        $projects = $this->getProjectData();
        $ret = array();
        foreach ($projects as $p) {
            $ret[$p->ID] = $p->pname;
        }
        return $ret;
    }

    public static function getCompanyList() {
        $companies = DB::connection('jira')->select(DB::raw("select * from projectcategory order by cname"));
        $ret = array();
        foreach ($companies as $co) {
            $ret[$co->ID] = $co->cname;
        }
        return $ret;
    }

    public static function getUserList() {
        $users = DB::connection('jira')->select(DB::raw("select lower_user_name from cwd_user order by lower_user_name"));
        $ret = array();
        foreach ($users as $u) {
            $ret[$u->lower_user_name] = $u->lower_user_name;
        }
        return $ret;
    }

	public static function getIssuesByEmployeeName($employeeName) {
        return DB::connection('jira')->select(DB::raw(
        "select Issue.ID as IssueID, Issue.pkey, Issue.SUMMARY, Issue.DESCRIPTION,
				Assignee.first_name as Assignee_first_name, Assignee.Last_name as Assignee_Last_name, Assignee.email_address as Assignee_email_address,
				Reporter.first_name as Reporter_first_name, Reporter.Last_name as Reporter_Last_name, Reporter.email_address as Reporter_email_address,
				project.ID as projectID, project.pname as project_name
            FROM jiraissue as Issue
			INNER JOIN cwd_user as Assignee on Assignee.user_name = Issue.ASSIGNEE
			INNER JOIN cwd_user as Reporter on Reporter.user_name = Issue.REPORTER
			INNER JOIN project on project.ID = Issue.PROJECT
            WHERE Issue.ASSIGNEE = :employeeName
        "), array('employeeName'=>$employeeName));
    }

	public static function getActiveIssuesForProject($projectExternalId) {
        return DB::connection('jira')->select(DB::raw(
        "SELECT *
            FROM jiraissue as Issue
            WHERE Issue.PROJECT = :projectId AND Issue.issuestatus IN (1, 3, 4) AND Issue.TIMEORIGINALESTIMATE IS NOT NULL AND Issue.TIMEORIGINALESTIMATE <> 0
        "), array('projectId'=>$projectExternalId));
    }

}
