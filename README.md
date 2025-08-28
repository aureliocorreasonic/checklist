mkdir checklist

cd checklist

git clone https://github.com/aureliocorreasonic/checklist.git

docker compose up -d

docker compose exec -T db mysql -uroot -p'Plp@2020' checklist_db < schema.sql
